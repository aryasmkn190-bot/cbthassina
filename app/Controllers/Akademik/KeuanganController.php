<?php

namespace App\Controllers\Akademik;

use App\Controllers\BaseController;
use App\Models\KeuanganSppModel;
use App\Models\PesertaModel;
use App\Models\KelasModel;
use Ramsey\Uuid\Uuid;

class KeuanganController extends BaseController
{
    protected $model;
    protected $pesertaModel;
    protected $kelasModel;
    protected $validation;

    public function __construct()
    {
        $this->model = new KeuanganSppModel();
        $this->pesertaModel = new PesertaModel();
        $this->kelasModel = new KelasModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $setting = $this->appSetting();
        $kelas = $this->kelasModel->getActiveSorted();
        $data = [
            'setting' => $setting,
            'title' => 'Manajemen Keuangan & SPP',
            'kelas' => $kelas,
        ];
        return view('Panel/Akademik/Keuangan/index', $data);
    }

    public function list()
    {
        if ($this->request->isAJAX()) {
            $kelas_id = $this->request->getGet('kelas_id');
            $data = $this->model->getWithPesertaAndKelas($kelas_id);
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function generateInvoice()
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'kelas_id' => 'required',
                'bulan'    => 'required|max_length[20]',
                'nominal'  => 'required|integer|greater_than[0]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $kelas_id = $this->request->getPost('kelas_id');
            $bulan = $this->request->getPost('bulan');
            $nominal = (int) $this->request->getPost('nominal');

            // Fetch students in this class
            $students = $this->pesertaModel->where('kelas_id', $kelas_id)->findAll();
            if (empty($students)) {
                return $this->response->setJSON(['status' => false, 'message' => 'Tidak ada siswa ditemukan di kelas yang dipilih.']);
            }

            $createdCount = 0;
            $skippedCount = 0;

            foreach ($students as $student) {
                // Check if invoice already exists for this student and month
                $exists = $this->model
                    ->where('peserta_id', $student['id'])
                    ->where('bulan', $bulan)
                    ->first();

                if ($exists) {
                    $skippedCount++;
                    continue;
                }

                // Generate unique invoice number: INV/SPP/[YEAR]/[RANDOM_HEX]
                $invoiceNum = 'INV/SPP/' . date('Y') . '/' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

                $this->model->insert([
                    'id'             => Uuid::uuid4()->toString(),
                    'peserta_id'     => $student['id'],
                    'bulan'          => $bulan,
                    'nominal'        => $nominal,
                    'status_bayar'   => 'belum_bayar',
                    'metode_bayar'   => null,
                    'tanggal_bayar'  => null,
                    'invoice_number' => $invoiceNum,
                    'created_at'     => date('Y-m-d H:i:s')
                ]);

                $createdCount++;
            }

            $msg = "$createdCount Invoice berhasil dibuat.";
            if ($skippedCount > 0) {
                $msg .= " ($skippedCount Siswa dilewati karena tagihan bulan ini sudah ada)";
            }

            return $this->response->setJSON(['status' => true, 'message' => $msg]);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function payInvoice($id = null)
    {
        if ($this->request->isAJAX()) {
            $rules = [
                'metode_bayar' => 'required|in_list[tunai,transfer,qris]'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['status' => false, 'message' => $this->validation->getErrors()]);
            }

            $invoice = $this->model->find($id);
            if (!$invoice) {
                return $this->response->setJSON(['status' => false, 'message' => 'Invoice tidak ditemukan.']);
            }

            if ($invoice['status_bayar'] === 'lunas') {
                return $this->response->setJSON(['status' => false, 'message' => 'Tagihan ini sudah lunas.']);
            }

            $this->model->update($id, [
                'status_bayar'  => 'lunas',
                'metode_bayar'  => $this->request->getPost('metode_bayar'),
                'tanggal_bayar' => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
            ]);

            return $this->response->setJSON(['status' => true, 'message' => 'Pembayaran tagihan berhasil dicatat.']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }

    public function delete($id = null)
    {
        if ($this->request->isAJAX()) {
            $this->model->delete($id);
            return $this->response->setJSON(['status' => true, 'message' => 'Tagihan berhasil dihapus.']);
        }
        return $this->response->setJSON(['status' => false, 'message' => 'Hanya bisa diakses via AJAX.']);
    }
}
