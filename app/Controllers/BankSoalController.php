<?php

namespace App\Controllers;

use Ramsey\Uuid\Uuid;
use App\Controllers\BaseController;
use App\Models\BankSoalModel;
use App\Models\UserModel;

class BankSoalController extends BaseController
{
    protected $bankSoalModel;
    protected $validation;

    public function __construct()
    {
        $this->bankSoalModel = new BankSoalModel();
        $this->validation    = \Config\Services::validation();
    }

    public function index()
    {

        $userModel = new UserModel();
        if (has_role('admin')) {
            $gurus = $userModel->where('roles', 'guru')->findAll();
        } elseif (has_role('guru')) {
            $userId = user_id();
            $gurus = [$userModel->find($userId)];
        } else {
            // Role tidak dikenali, tolak akses
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title'      => 'Bank Soal',
            'gurus'      => $gurus,

            'setting'    => $this->appSetting(),
        ];

        return view('Panel/BankSoal/bank_soal_view', $data);
    }


    public function getAll()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => false,
                'message' => 'Hanya bisa diakses via AJAX.'
            ]);
        }

        if (!is_logged_in()) {
            return $this->response->setStatusCode(401)->setJSON([
                'status'  => false,
                'message' => 'Silakan login terlebih dahulu.'
            ]);
        }

        // Ambil data berdasarkan role
        if (has_role('admin')) {
            $data = $this->bankSoalModel->getAll(); // semua data
        } else {
            $data = $this->bankSoalModel->getAll(user_id()); // data milik guru
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $data
        ]);
    }


    public function create()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'kode'       => 'required|min_length[2]|max_length[50]|is_unique[bank_soal.kode]',
                'nama'       => 'required|min_length[3]|max_length[255]',
                'deskripsi'  => 'permit_empty',
                'is_active'  => 'in_list[0,1]',
                'created_by' => 'required|min_length[36]|max_length[36]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => $this->validation->getErrors()
                ]);
            }

            $data = [
                'id'         => Uuid::uuid4()->toString(),
                'kode'       => $this->request->getPost('kode'),
                'nama'       => $this->request->getPost('nama'),
                'deskripsi'  => $this->request->getPost('deskripsi'),
                'is_active'  => $this->request->getPost('is_active') ?? 1,
                'is_public'  => $this->request->getPost('is_public') ?? 1,
                'created_by' => $this->request->getPost('created_by'),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $this->bankSoalModel->insert($data);

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Bank soal berhasil ditambahkan.'
            ]);
        }

        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function update($id = null)
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'kode'       => "required|min_length[2]|max_length[50]|is_unique[bank_soal.kode,id,{$id}]",
                'nama'       => 'required|min_length[3]|max_length[255]',
                'deskripsi'  => 'permit_empty',
                'is_active'  => 'in_list[0,1]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => $this->validation->getErrors()
                ]);
            }

            $data = [
                'kode'       => $this->request->getPost('kode'),
                'nama'       => $this->request->getPost('nama'),
                'deskripsi'  => $this->request->getPost('deskripsi'),
                'is_active'  => $this->request->getPost('is_active') ?? 1,
                'is_public'  => $this->request->getPost('is_public') ?? 1,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $this->bankSoalModel->update($id, $data);

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Bank soal berhasil diperbarui.'
            ]);
        }

        return $this->fail('Hanya bisa diakses via AJAX.');
    }
    public function update_visibility($id = null)
    {
        if ($this->request->isAJAX()) {
            $is_public = $this->request->getPost('is_public');

            // Validasi
            if (!in_array($is_public, ['0', '1'], true)) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Nilai visibilitas tidak valid.'
                ]);
            }

            // Update field is_public
            $this->bankSoalModel->update($id, [
                'is_public'  => $is_public,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Visibilitas bank soal berhasil diperbarui.'
            ]);
        }

        return $this->fail('Hanya bisa diakses via AJAX.');
    }


    public function delete($id = null)
    {
        if ($this->request->isAJAX()) {
            // Cek apakah sedang dipakai di ujian
            $db = \Config\Database::connect();
            $used = $db->table('ujian')->where('bank_soal_id', $id)->countAllResults();

            if ($used > 0) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Bank soal tidak dapat dihapus karena sedang digunakan.'
                ]);
            }

            $this->bankSoalModel->delete($id);
            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Bank soal berhasil dihapus.'
            ]);
        }

        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function duplicate($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => false,
                'message' => 'Hanya bisa diakses via AJAX.'
            ]);
        }

        if (!is_logged_in()) {
            return $this->response->setStatusCode(401)->setJSON([
                'status'  => false,
                'message' => 'Silakan login terlebih dahulu.'
            ]);
        }

        $original = $this->bankSoalModel->find($id);
        if (!$original) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Bank soal tidak ditemukan.'
            ]);
        }

        // Cek otorisasi: hanya admin, pembuat bank soal, atau jika bank soal public
        if (!has_role('admin') && $original['created_by'] !== user_id() && $original['is_public'] != 1) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Anda tidak memiliki akses untuk menduplikasi bank soal ini.'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Generate ID Baru dan Kode Unik Baru
        $newBankSoalId = Uuid::uuid4()->toString();
        $newKode = $original['kode'] . '-COPY';
        $suffix = 1;
        while ($this->bankSoalModel->where('kode', $newKode)->countAllResults() > 0) {
            $newKode = $original['kode'] . '-COPY' . $suffix;
            $suffix++;
        }

        // 2. Insert Bank Soal Baru
        $newBankSoalData = [
            'id'         => $newBankSoalId,
            'kode'       => $newKode,
            'nama'       => $original['nama'] . ' - COPY',
            'deskripsi'  => $original['deskripsi'],
            'is_active'  => $original['is_active'],
            'is_public'  => $original['is_public'],
            'created_by' => user_id(), // Pembuat diubah menjadi user yang sedang login
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $db->table('bank_soal')->insert($newBankSoalData);

        // 3. Duplicate Topik Soal
        $topikModel = new \App\Models\TopikSoalModel();
        $originalTopiks = $topikModel->where('bank_soal_id', $id)->findAll();
        $topicMapping = [];

        foreach ($originalTopiks as $topik) {
            $newTopikId = Uuid::uuid4()->toString();
            $topicMapping[$topik['id']] = $newTopikId;

            $newTopikData = [
                'id'           => $newTopikId,
                'bank_soal_id' => $newBankSoalId,
                'nama'         => $topik['nama'],
                'keterangan'   => $topik['keterangan'],
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ];
            $db->table('topik_soal')->insert($newTopikData);
        }

        // 4. Duplicate Soal
        $soalModel = new \App\Models\SoalModel();
        $originalSoals = $soalModel->where('bank_soal_id', $id)->findAll();

        foreach ($originalSoals as $soal) {
            $newSoalId = Uuid::uuid4()->toString();

            // Pemetaan topik jika ada
            $newTopicId = null;
            if (!empty($soal['topik_soal_id']) && isset($topicMapping[$soal['topik_soal_id']])) {
                $newTopicId = $topicMapping[$soal['topik_soal_id']];
            }

            $newSoalData = [
                'id'            => $newSoalId,
                'bank_soal_id'  => $newBankSoalId,
                'soal_no'       => $soal['soal_no'],
                'jenis_soal'    => $soal['jenis_soal'],
                'pertanyaan'    => $soal['pertanyaan'],
                'jawaban'       => $soal['jawaban'],
                'topik_soal_id' => $newTopicId,
                'bobot'         => $soal['bobot'],
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ];
            $db->table('soal')->insert($newSoalData);

            // 5. Duplicate Opsi Soal
            $originalOpsiList = $db->table('soal_opsi')->where('soal_id', $soal['id'])->get()->getResultArray();
            foreach ($originalOpsiList as $opsi) {
                $newOpsiData = [
                    'id'         => Uuid::uuid4()->toString(),
                    'soal_id'    => $newSoalId,
                    'label'      => $opsi['label'],
                    'teks'       => $opsi['teks'],
                    'pasangan'   => $opsi['pasangan'],
                    'is_true'    => $opsi['is_true'],
                    'bobot'      => $opsi['bobot'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $db->table('soal_opsi')->insert($newOpsiData);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Gagal menduplikasi bank soal.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Bank soal berhasil diduplikasi.'
        ]);
    }

    private function fail($message)
    {
        return $this->response->setJSON([
            'status'  => false,
            'message' => $message
        ]);
    }
}
