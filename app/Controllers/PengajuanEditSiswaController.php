<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PengajuanEditSiswaModel;
use App\Models\PesertaModel;

class PengajuanEditSiswaController extends BaseController
{
    protected $pengajuanModel;
    protected $pesertaModel;
    protected $validation;

    public function __construct()
    {
        $this->pengajuanModel = new PengajuanEditSiswaModel();
        $this->pesertaModel = new PesertaModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $setting = $this->appSetting();

        $data = [
            'setting' => $setting,
            'title'   => 'Pengajuan Perubahan Data Siswa',
        ];

        return view('Panel/Kesiswaan/pengajuan_edit_siswa', $data);
    }

    public function list()
    {
        if ($this->request->isAJAX()) {
            $data = $this->pengajuanModel->getWithRelations();
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function verify()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'id'            => 'required|min_length[36]|max_length[36]',
                'status'        => 'required|in_list[disetujui,ditolak]',
                'catatan_admin' => 'permit_empty|max_length[255]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $id = $this->request->getPost('id');
            $status = $this->request->getPost('status');
            $catatan = $this->request->getPost('catatan_admin') ?: null;

            $request = $this->pengajuanModel->find($id);
            if (!$request) {
                return $this->response->setJSON(['status' => false, 'message' => 'Pengajuan tidak ditemukan.']);
            }

            if ($request['status'] !== 'menunggu') {
                return $this->response->setJSON(['status' => false, 'message' => 'Pengajuan ini sudah diverifikasi sebelumnya.']);
            }

            if ($status === 'disetujui') {
                $dataBaru = json_decode($request['data_baru'], true);
                if (empty($dataBaru)) {
                    return $this->response->setJSON(['status' => false, 'message' => 'Format data pengajuan baru tidak valid.']);
                }

                // Update the student database record with new verified data
                $dataBaru['updated_at'] = date('Y-m-d H:i:s');
                $this->pesertaModel->update($request['peserta_id'], $dataBaru);
                
                $msg = 'Pengajuan disetujui. Biodata pokok siswa telah diperbarui.';
            } else {
                $msg = 'Pengajuan ditolak.';
            }

            // Update request status
            $this->pengajuanModel->update($id, [
                'status'        => $status,
                'catatan_admin' => $catatan,
                'updated_at'    => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['status' => true, 'message' => $msg]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }
}
