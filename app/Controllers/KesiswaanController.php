<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PrestasiModel;
use App\Models\PelanggaranModel;
use App\Models\PesertaModel;
use Ramsey\Uuid\Uuid;

class KesiswaanController extends BaseController
{
    protected $prestasiModel;
    protected $pelanggaranModel;
    protected $pesertaModel;
    protected $validation;

    public function __construct()
    {
        $this->prestasiModel = new PrestasiModel();
        $this->pelanggaranModel = new PelanggaranModel();
        $this->pesertaModel = new PesertaModel();
        $this->validation = \Config\Services::validation();
    }

    // ================== PRESTASI ==================
    public function prestasi()
    {
        $setting = $this->appSetting();
        $peserta = $this->pesertaModel->orderBy('nama', 'ASC')->findAll();
        
        $data = [
            'setting' => $setting,
            'title'   => 'Prestasi Kesiswaan',
            'peserta' => $peserta
        ];
        return view('Panel/Kesiswaan/prestasi', $data);
    }

    public function prestasiList()
    {
        if ($this->request->isAJAX()) {
            $data = $this->prestasiModel->getWithPeserta();
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function prestasiCreate()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'peserta_id'    => 'required|min_length[36]|max_length[36]',
                'nama_prestasi' => 'required|max_length[255]',
                'tingkat'       => 'required|in_list[Sekolah,Kabupaten,Provinsi,Nasional,Internasional]',
                'kategori'      => 'required|in_list[Akademik,Non-Akademik]',
                'tanggal'       => 'required|valid_date[Y-m-d]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $id = $this->request->getPost('id');
            $data = [
                'peserta_id'    => $this->request->getPost('peserta_id'),
                'nama_prestasi' => $this->request->getPost('nama_prestasi'),
                'tingkat'       => $this->request->getPost('tingkat'),
                'kategori'      => $this->request->getPost('kategori'),
                'tanggal'       => $this->request->getPost('tanggal'),
            ];

            if ($id) {
                $data['updated_at'] = date('Y-m-d H:i:s');
                $this->prestasiModel->update($id, $data);
                $msg = 'Prestasi berhasil diperbarui.';
            } else {
                $data['id'] = Uuid::uuid4()->toString();
                $data['created_at'] = date('Y-m-d H:i:s');
                $this->prestasiModel->insert($data);
                $msg = 'Prestasi berhasil ditambahkan.';
            }

            return $this->response->setJSON(['status' => true, 'message' => $msg]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function prestasiDelete($id = null)
    {
        if ($this->request->isAJAX()) {
            $this->prestasiModel->delete($id);
            return $this->response->setJSON(['status' => true, 'message' => 'Data prestasi berhasil dihapus.']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    // ================== PELANGGARAN ==================
    public function pelanggaran()
    {
        $setting = $this->appSetting();
        $peserta = $this->pesertaModel->orderBy('nama', 'ASC')->findAll();
        
        $data = [
            'setting' => $setting,
            'title'   => 'Poin & Pelanggaran Siswa',
            'peserta' => $peserta
        ];
        return view('Panel/Kesiswaan/pelanggaran', $data);
    }

    public function pelanggaranList()
    {
        if ($this->request->isAJAX()) {
            $data = $this->pelanggaranModel->getWithPeserta();
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function pelanggaranCreate()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'peserta_id'       => 'required|min_length[36]|max_length[36]',
                'nama_pelanggaran' => 'required|max_length[255]',
                'kategori'         => 'required|in_list[Ringan,Sedang,Berat]',
                'point'            => 'required|integer|greater_than_equal_to[0]',
                'tanggal'          => 'required|valid_date[Y-m-d]',
                'tindakan'         => 'permit_empty|max_length[255]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $id = $this->request->getPost('id');
            $data = [
                'peserta_id'       => $this->request->getPost('peserta_id'),
                'nama_pelanggaran' => $this->request->getPost('nama_pelanggaran'),
                'kategori'         => $this->request->getPost('kategori'),
                'point'            => $this->request->getPost('point'),
                'tanggal'          => $this->request->getPost('tanggal'),
                'tindakan'         => $this->request->getPost('tindakan'),
            ];

            if ($id) {
                $data['updated_at'] = date('Y-m-d H:i:s');
                $this->pelanggaranModel->update($id, $data);
                $msg = 'Poin pelanggaran berhasil diperbarui.';
            } else {
                $data['id'] = Uuid::uuid4()->toString();
                $data['created_at'] = date('Y-m-d H:i:s');
                $this->pelanggaranModel->insert($data);
                $msg = 'Poin pelanggaran berhasil ditambahkan.';
            }

            return $this->response->setJSON(['status' => true, 'message' => $msg]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function pelanggaranDelete($id = null)
    {
        if ($this->request->isAJAX()) {
            $this->pelanggaranModel->delete($id);
            return $this->response->setJSON(['status' => true, 'message' => 'Pelanggaran berhasil dihapus.']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }
}
