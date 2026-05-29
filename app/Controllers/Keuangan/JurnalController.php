<?php

namespace App\Controllers\Keuangan;

use App\Controllers\BaseController;
use App\Models\JurnalModel;
use Ramsey\Uuid\Uuid;

class JurnalController extends BaseController
{
    protected $model;
    protected $validation;

    public function __construct()
    {
        $this->model = new JurnalModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $setting = $this->appSetting();

        // Calculate metrics
        $transactions = $this->model->findAll();
        $totalDebit = 0;
        $totalKredit = 0;

        foreach ($transactions as $t) {
            if ($t['tipe'] === 'debit') {
                $totalDebit += (float)$t['nominal'];
            } else {
                $totalKredit += (float)$t['nominal'];
            }
        }

        $balance = $totalDebit - $totalKredit;

        // Categories for breakdown
        $debitCategories = [];
        $kreditCategories = [];
        foreach ($transactions as $t) {
            $cat = esc($t['kategori']);
            $nom = (float)$t['nominal'];
            if ($t['tipe'] === 'debit') {
                $debitCategories[$cat] = ($debitCategories[$cat] ?? 0) + $nom;
            } else {
                $kreditCategories[$cat] = ($kreditCategories[$cat] ?? 0) + $nom;
            }
        }

        $data = [
            'setting'          => $setting,
            'title'            => 'Jurnal & Laporan Keuangan',
            'totalDebit'       => $totalDebit,
            'totalKredit'      => $totalKredit,
            'balance'          => $balance,
            'debitCategories'  => $debitCategories,
            'kreditCategories' => $kreditCategories,
        ];

        return view('Panel/Keuangan/jurnal', $data);
    }

    public function list()
    {
        if ($this->request->isAJAX()) {
            $data = $this->model->orderBy('tanggal', 'DESC')->findAll();
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function create()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'tanggal'    => 'required|valid_date[Y-m-d]',
                'keterangan' => 'required|max_length[255]',
                'tipe'       => 'required|in_list[debit,kredit]',
                'nominal'    => 'required|numeric|greater_than[0]',
                'kategori'   => 'required|max_length[100]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $this->model->insert([
                'id'         => Uuid::uuid4()->toString(),
                'tanggal'    => $this->request->getPost('tanggal'),
                'keterangan' => $this->request->getPost('keterangan'),
                'tipe'       => $this->request->getPost('tipe'),
                'nominal'    => $this->request->getPost('nominal'),
                'kategori'   => $this->request->getPost('kategori'),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['status' => true, 'message' => 'Transaksi berhasil dicatat.']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function delete($id = null)
    {
        if ($this->request->isAJAX()) {
            $this->model->delete($id);
            return $this->response->setJSON(['status' => true, 'message' => 'Transaksi berhasil dihapus.']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }
}
