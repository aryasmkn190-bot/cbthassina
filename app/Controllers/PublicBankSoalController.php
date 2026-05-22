<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BankSoalModel;

class PublicBankSoalController extends BaseController
{
    protected $bankSoalModel;

    public function __construct()
    {
        $this->bankSoalModel = new BankSoalModel();
    }

    /**
     * Halaman daftar bank soal publik
     */
    public function index()
    {
        $data = [
            'title'   => 'Bank Soal Publik',
            'setting' => $this->appSetting()
        ];

        return view('Public/BankSoal/bank_soal_view', $data);
    }

    /**
     * Ambil semua bank soal publik (via AJAX)
     */
    public function getAll()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => false,
                'message' => 'Hanya bisa diakses via AJAX.'
            ]);
        }

        // Ambil data bank soal yang aktif dan publik
        $data = $this->bankSoalModel
            ->where('is_public', 1)
            ->where('is_active', 1)
            ->findAll();

        return $this->response->setJSON([
            'status' => true,
            'data'   => $data
        ]);
    }

    /**
     * Detail bank soal publik
     */
    public function detail($id = null)
    {
        if (!$id) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $soal = $this->bankSoalModel
            ->where('is_public', 1)
            ->where('is_active', 1)
            ->find($id);

        if (!$soal) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('Public/BankSoal/bank_soal_detail', [
            'title' => 'Detail Bank Soal',
            'soal'  => $soal
        ]);
    }

    /**
     * Response gagal
     */
    private function fail($message)
    {
        return $this->response->setJSON([
            'status'  => false,
            'message' => $message
        ]);
    }
}
