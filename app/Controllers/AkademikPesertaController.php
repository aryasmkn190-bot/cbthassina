<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JadwalPelajaranModel;
use App\Models\TugasModel;
use App\Models\TugasJawabanModel;
use App\Models\AbsensiModel;
use App\Models\RaporNilaiModel;
use App\Models\KeuanganSppModel;
use Ramsey\Uuid\Uuid;

class AkademikPesertaController extends BaseController
{
    protected $session;
    protected $pesertaId;
    protected $kelasId;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->session = session();
        $peserta = $this->session->get('peserta');
        $this->pesertaId = $peserta['id'] ?? null;
        $this->kelasId = $peserta['kelas_id'] ?? null;
    }

    private function checkAuth()
    {
        if (!$this->pesertaId) {
            return false;
        }
        return true;
    }

    public function getJadwal()
    {
        if (!$this->checkAuth()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized.']);
        }

        $model = new JadwalPelajaranModel();
        // Fetch schedule for the student's class
        $data = $model->select('jadwal_pelajaran.*, mata_pelajaran.nama as mata_pelajaran')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_pelajaran.mata_pelajaran_id', 'left')
            ->where('kelas_id', $this->kelasId)
            ->orderBy('waktu_mulai', 'ASC')
            ->findAll();

        return $this->response->setJSON(['status' => true, 'data' => $data]);
    }

    public function getTugas()
    {
        if (!$this->checkAuth()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized.']);
        }

        $model = new TugasModel();
        // Fetch assignments for the student's class
        $tugas = $model->select('tugas.*, mata_pelajaran.nama as mata_pelajaran')
            ->join('mata_pelajaran', 'mata_pelajaran.id = tugas.mata_pelajaran_id', 'left')
            ->where('kelas_id', $this->kelasId)
            ->orderBy('tugas.created_at', 'DESC')
            ->findAll();

        $jawabanModel = new TugasJawabanModel();
        
        // Fetch student's submissions
        $submissions = $jawabanModel->where('peserta_id', $this->pesertaId)->findAll();
        $submissionsMap = [];
        foreach ($submissions as $sub) {
            $submissionsMap[$sub['tugas_id']] = $sub;
        }

        foreach ($tugas as &$t) {
            $t['submission'] = $submissionsMap[$t['id']] ?? null;
        }

        return $this->response->setJSON(['status' => true, 'data' => $tugas]);
    }

    public function submitTugas()
    {
        if (!$this->checkAuth()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized.']);
        }

        $validationRule = [
            'tugas_id' => 'required',
            'file' => [
                'label' => 'File Jawaban',
                'rules' => 'uploaded[file]|max_size[file,10240]|ext_in[file,pdf,doc,docx,zip,rar,png,jpg,jpeg]',
            ],
        ];

        if (!$this->validate($validationRule)) {
            return $this->response->setJSON(['status' => false, 'message' => $this->validator->getErrors()]);
        }

        $tugasId = $this->request->getPost('tugas_id');
        $file = $this->request->getFile('file');

        if ($file->isValid() && !$file->hasMoved()) {
            // Check if directory exists
            $uploadPath = ROOTPATH . 'public/uploads/tugas/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            $filePath = 'uploads/tugas/' . $newName;

            $jawabanModel = new TugasJawabanModel();
            
            // Check if already submitted
            $existing = $jawabanModel->where('tugas_id', $tugasId)->where('peserta_id', $this->pesertaId)->first();

            if ($existing) {
                // Delete old file if exists
                if (file_exists(ROOTPATH . 'public/' . $existing['file_path'])) {
                    @unlink(ROOTPATH . 'public/' . $existing['file_path']);
                }

                $jawabanModel->update($existing['id'], [
                    'file_path' => $filePath,
                    'tanggal_kirim' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                $jawabanModel->insert([
                    'id' => Uuid::uuid4()->toString(),
                    'tugas_id' => $tugasId,
                    'peserta_id' => $this->pesertaId,
                    'file_path' => $filePath,
                    'catatan_guru' => null,
                    'nilai' => 0,
                    'tanggal_kirim' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            return $this->response->setJSON(['status' => true, 'message' => 'Tugas berhasil dikumpulkan.']);
        }

        return $this->response->setJSON(['status' => false, 'message' => 'Terjadi kesalahan saat mengunggah file.']);
    }

    public function scanAbsen()
    {
        if (!$this->checkAuth()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized.']);
        }

        $qrCode = $this->request->getPost('qrcode');
        if (empty($qrCode)) {
            return $this->response->setJSON(['status' => false, 'message' => 'QR Code tidak boleh kosong.']);
        }

        // Try decrypting QR code (base64 decode + key replacement check)
        $key = $this->appSetting()->key_encrypt ?? 'cbthassina';
        $decrypted = base64_decode($qrCode);
        
        if (str_contains($decrypted, $key)) {
            $rawPayload = str_replace($key, '', $decrypted);
            $parts = explode('|', $rawPayload);

            if (count($parts) === 2) {
                $kelasId = $parts[0];
                $date = $parts[1];

                // Verify class matches the student's class
                if ($kelasId !== $this->kelasId) {
                    return $this->response->setJSON(['status' => false, 'message' => 'QR Code ini bukan untuk kelas Anda.']);
                }

                // Verify date is today
                if ($date !== date('Y-m-d')) {
                    return $this->response->setJSON(['status' => false, 'message' => 'QR Code ini sudah kadaluarsa.']);
                }

                $absensiModel = new AbsensiModel();
                // Check if already checked in today
                $exists = $absensiModel->where('peserta_id', $this->pesertaId)
                    ->where('tanggal', $date)
                    ->first();

                if ($exists) {
                    return $this->response->setJSON(['status' => true, 'message' => 'Anda sudah tercatat hadir hari ini.', 'already' => true]);
                }

                // Determine late or on-time status based on scan time
                $scanTime = date('H:i:s');
                // Let's assume school starts at 07:30:00
                $status = 'hadir';

                $absensiModel->insert([
                    'id' => Uuid::uuid4()->toString(),
                    'peserta_id' => $this->pesertaId,
                    'tanggal' => $date,
                    'waktu_scan' => $scanTime,
                    'status' => $status,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                return $this->response->setJSON(['status' => true, 'message' => 'Presensi berhasil dicatat!', 'time' => $scanTime]);
            }
        }

        return $this->response->setJSON(['status' => false, 'message' => 'QR Code tidak valid atau format salah.']);
    }

    public function getRapor()
    {
        if (!$this->checkAuth()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized.']);
        }

        $semester = $this->request->getGet('semester') ?: 'Ganjil';
        $tahunAjaran = $this->request->getGet('tahun_ajaran') ?: '2025/2026';

        $model = new RaporNilaiModel();
        $grades = $model->select('rapor_nilai.*, mata_pelajaran.nama as mata_pelajaran')
            ->join('mata_pelajaran', 'mata_pelajaran.id = rapor_nilai.mata_pelajaran_id', 'left')
            ->where('peserta_id', $this->pesertaId)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->orderBy('mata_pelajaran.nama', 'ASC')
            ->findAll();

        return $this->response->setJSON(['status' => true, 'data' => $grades]);
    }

    public function getKeuangan()
    {
        if (!$this->checkAuth()) {
            return $this->response->setJSON(['status' => false, 'message' => 'Unauthorized.']);
        }

        $model = new KeuanganSppModel();
        $invoices = $model->where('peserta_id', $this->pesertaId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $this->response->setJSON(['status' => true, 'data' => $invoices]);
    }

    private function getPesertaData()
    {
        $pesertaModel = new \App\Models\PesertaModel();
        return $pesertaModel
            ->select('peserta.*, kelas.nama as kelas')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
            ->find($this->pesertaId);
    }

    public function jadwalView()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $model = new JadwalPelajaranModel();
        $jadwal = $model->select('jadwal_pelajaran.*, mata_pelajaran.nama as mata_pelajaran')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_pelajaran.mata_pelajaran_id', 'left')
            ->where('kelas_id', $this->kelasId)
            ->orderBy('waktu_mulai', 'ASC')
            ->findAll();

        return view('Siswa/jadwal', [
            'setting' => $this->appSetting(),
            'title'   => 'Jadwal Pelajaran',
            'peserta' => $this->getPesertaData(),
            'jadwal'  => $jadwal
        ]);
    }

    public function tugasView()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $model = new TugasModel();
        $tugas = $model->select('tugas.*, mata_pelajaran.nama as mata_pelajaran')
            ->join('mata_pelajaran', 'mata_pelajaran.id = tugas.mata_pelajaran_id', 'left')
            ->where('kelas_id', $this->kelasId)
            ->orderBy('tugas.created_at', 'DESC')
            ->findAll();

        $jawabanModel = new TugasJawabanModel();
        $submissions = $jawabanModel->where('peserta_id', $this->pesertaId)->findAll();
        $submissionsMap = [];
        foreach ($submissions as $sub) {
            $submissionsMap[$sub['tugas_id']] = $sub;
        }

        foreach ($tugas as &$t) {
            $t['submission'] = $submissionsMap[$t['id']] ?? null;
        }

        return view('Siswa/tugas', [
            'setting' => $this->appSetting(),
            'title'   => 'Tugas & PR (LMS)',
            'peserta' => $this->getPesertaData(),
            'tugas'   => $tugas
        ]);
    }

    public function materiView()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('Siswa/materi', [
            'setting' => $this->appSetting(),
            'title'   => 'Materi Belajar',
            'peserta' => $this->getPesertaData()
        ]);
    }

    public function absensiView()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $absensiModel = new AbsensiModel();
        $logs = $absensiModel->where('peserta_id', $this->pesertaId)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('waktu_scan', 'DESC')
            ->findAll();

        return view('Siswa/absensi', [
            'setting' => $this->appSetting(),
            'title'   => 'Absensi QR',
            'peserta' => $this->getPesertaData(),
            'logs'    => $logs
        ]);
    }

    public function raporView()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $semester = $this->request->getGet('semester') ?: 'Genap';
        $tahunAjaran = $this->request->getGet('tahun_ajaran') ?: '2025/2026';

        $model = new RaporNilaiModel();
        $grades = $model->select('rapor_nilai.*, mata_pelajaran.nama as mata_pelajaran')
            ->join('mata_pelajaran', 'mata_pelajaran.id = rapor_nilai.mata_pelajaran_id', 'left')
            ->where('peserta_id', $this->pesertaId)
            ->where('rapor_nilai.semester', $semester)
            ->where('rapor_nilai.tahun_ajaran', $tahunAjaran)
            ->orderBy('mata_pelajaran.nama', 'ASC')
            ->findAll();

        return view('Siswa/rapor', [
            'setting'     => $this->appSetting(),
            'title'       => 'Rapor Digital',
            'peserta'     => $this->getPesertaData(),
            'grades'      => $grades,
            'semester'    => $semester,
            'tahunAjaran' => $tahunAjaran
        ]);
    }

    public function keuanganView()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $model = new KeuanganSppModel();
        $invoices = $model->where('peserta_id', $this->pesertaId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('Siswa/keuangan', [
            'setting'  => $this->appSetting(),
            'title'    => 'Keuangan & SPP',
            'peserta'  => $this->getPesertaData(),
            'invoices' => $invoices
        ]);
    }

    public function kesiswaanView()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $prestasiModel = new \App\Models\PrestasiModel();
        $pelanggaranModel = new \App\Models\PelanggaranModel();

        $prestasi = $prestasiModel->where('peserta_id', $this->pesertaId)
            ->orderBy('tanggal', 'DESC')
            ->findAll();

        $pelanggaran = $pelanggaranModel->where('peserta_id', $this->pesertaId)
            ->orderBy('tanggal', 'DESC')
            ->findAll();

        return view('Siswa/kesiswaan', [
            'setting'     => $this->appSetting(),
            'title'       => 'Kesiswaan (Prestasi & Poin)',
            'peserta'     => $this->getPesertaData(),
            'prestasi'    => $prestasi,
            'pelanggaran' => $pelanggaran
        ]);
    }

    public function ekstraView()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $ekstraModel = new \App\Models\EkstraModel();
        $ekstras = $ekstraModel->orderBy('nama_ekstra', 'ASC')->findAll();

        return view('Siswa/ekstra', [
            'setting' => $this->appSetting(),
            'title'   => 'Ekstrakurikuler',
            'peserta' => $this->getPesertaData(),
            'ekstras' => $ekstras
        ]);
    }

    public function infoView()
    {
        if (!$this->checkAuth()) {
            return redirect()->to('auth/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('Siswa/info', [
            'setting' => $this->appSetting(),
            'title'   => 'Info & Pengumuman Sekolah',
            'peserta' => $this->getPesertaData()
        ]);
    }
}
