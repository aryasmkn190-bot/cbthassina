<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TopikSoalModel;
use Ramsey\Uuid\Uuid;

class TopikSoalController extends BaseController
{
    protected $model;
    protected $validation;

    public function __construct()
    {
        $this->model = new TopikSoalModel();
        $this->validation = \Config\Services::validation();
    }

    public function index($banksoalid)
    {
        $setting = $this->appSetting();
        $bankSoalModel = new \App\Models\BankSoalModel();
        $bankSoal = $bankSoalModel->find($banksoalid);
        if (!$bankSoal) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        if (!has_role('admin') && $bankSoal['created_by'] !== user_id()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'setting'     => $setting,
            'title'       => 'Manajemen Topik Soal',
            'banksoalid'   => $banksoalid
        ];

        return view('Panel/Topik/topik_view', $data);
    }



    public function getAll($banksoalid)
    {
        if ($this->request->isAJAX()) {
            $data = $this->model
                ->where('bank_soal_id', $banksoalid)
                ->orderBy('created_at', 'DESC')
                ->findAll();

            return $this->response->setJSON(['status' => true, 'data' => $data]);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }


    public function create()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'bank_soal_id' => 'required',
                'nama'         => 'required|max_length[100]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $this->model->insert([
                'id'           => Uuid::uuid4()->toString(),
                'bank_soal_id' => $this->request->getPost('bank_soal_id'),
                'nama'         => $this->request->getPost('nama'),
                'keterangan'   => $this->request->getPost('keterangan'),
                'created_at'   => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil ditambahkan.']);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function update($id = null)
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'bank_soal_id' => 'required',
                'nama'         => "required|max_length[100]",
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $this->model->update($id, [
                'bank_soal_id' => $this->request->getPost('bank_soal_id'),
                'nama'         => $this->request->getPost('nama'),
                'keterangan'   => $this->request->getPost('keterangan'),
                'updated_at'   => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil diperbarui.']);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    public function delete($id = null)
    {
        if ($this->request->isAJAX()) {
            $this->model->delete($id);
            return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil dihapus.']);
        }
        return $this->fail('Hanya bisa diakses via AJAX.');
    }

    private function fail($message)
    {
        return $this->response->setJSON(['status' => false, 'message' => $message]);
    }
}
