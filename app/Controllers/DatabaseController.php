<?php

namespace App\Controllers;

class DatabaseController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $setting = $this->appSetting();

        // Daftar tabel yang ingin dihitung
        $tables = [
            'peserta',
            'kelas',
            'jurusan',
            'tingkat',
            'agama',
            'jawaban',
            'ruang',
            'bank_soal',
            'topik_soal',
            'soal',
            'soal_opsi',
            'ujian',
            'hasil_ujian',
            'sesi',
            'jenis_ujian'
        ];

        $db = \Config\Database::connect();
        $counts = [];

        foreach ($tables as $table) {
            try {
                // Cek apakah tabel ada
                if ($db->tableExists($table)) {
                    $counts[$table] = $db->table($table)->countAllResults(false);
                } else {
                    $counts[$table] = 0; // Jika tabel tidak ada
                }
            } catch (\Throwable $e) {
                // Jika error (misal tabel rusak), tetap aman
                $counts[$table] = 0;
            }
        }

        $data = [
            'setting' => $setting,
            'title'   => 'Database Aplikasi',
            'counts'  => $counts   // ← kirim ke view
        ];

        return view('Panel/Database/data_view', $data);
    }


    public function exportJson()
    {
        // Ambil nama tabel dari parameter GET, misalnya ?tables=peserta,kelas,jurusan
        $tables = $this->request->getGet('tables');

        if (!$tables) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => false,
                'message' => 'Parameter "tables" tidak ditemukan.'
            ]);
        }

        // Ubah string menjadi array
        $tableList = explode(',', $tables);

        // Tambahkan tabel 'settings' jika belum ada
        if (!in_array('settings', $tableList)) {
            $tableList[] = 'settings';
        }

        $data = [];

        foreach ($tableList as $table) {
            try {
                $query = $this->db->table($table)->get();
                $data[$table] = $query->getResultArray();
            } catch (\Throwable $e) {
                // Skip tabel yang error (misal typo atau tidak ada)
                $data[$table] = ['error' => $e->getMessage()];
            }
        }

        $jsonOutput = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $filename = 'backup_' . date('Ymd_His') . '.json';

        return $this->response
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($jsonOutput);
    }

    public function restoreJson()
    {
        $file = $this->request->getFile('backup_json');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => false, 'message' => 'File tidak valid'])->setStatusCode(400);
        }

        $content = file_get_contents($file->getTempName());
        $json = json_decode($content, true);

        if (!$json || !is_array($json)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Format file JSON tidak valid'])->setStatusCode(400);
        }

        try {
            $this->db->disableForeignKeyChecks();

            foreach ($json as $table => $rows) {
                // Kosongkan tabel dulu
                $this->db->table($table)->truncate();

                if (!empty($rows)) {
                    $this->db->table($table)->insertBatch($rows);
                }
            }

            $this->db->enableForeignKeyChecks();

            return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil direstore dari JSON.']);
        } catch (\Throwable $e) {
            $this->db->enableForeignKeyChecks();
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Restore gagal: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function exportSql()
    {
        $tables = $this->request->getGet('tables');

        if (!$tables) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => false,
                'message' => 'Parameter "tables" tidak ditemukan.'
            ]);
        }

        $tableList = explode(',', $tables);

        // Tambahkan tabel 'settings' jika belum ada
        if (!in_array('settings', $tableList)) {
            $tableList[] = 'settings';
        }

        $filename = 'backup_' . date('Ymd_His') . '.sql';

        $this->response->setHeader('Content-Type', 'application/sql');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        $tempFilePath = tempnam(sys_get_temp_dir(), 'sql_backup_');
        $handle = fopen($tempFilePath, 'w');

        if (!$handle) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => false,
                'message' => 'Gagal membuat file backup sementara.'
            ]);
        }

        fwrite($handle, "-- Backup Database CBT\n");
        fwrite($handle, "-- Generated: " . date('Y-m-d H:i:s') . "\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS = 0;\n\n");

        foreach ($tableList as $table) {
            if (!$this->db->tableExists($table)) {
                continue;
            }

            // 1. Structure
            $query = $this->db->query("SHOW CREATE TABLE " . $this->db->escapeIdentifiers($table));
            $row = $query->getRowArray();
            if (isset($row['Create Table'])) {
                fwrite($handle, "DROP TABLE IF EXISTS " . $this->db->escapeIdentifiers($table) . ";\n");
                fwrite($handle, $row['Create Table'] . ";\n\n");
            }

            // 2. Data
            $builder = $this->db->table($table);
            $totalRows = $builder->countAllResults(false);

            if ($totalRows > 0) {
                $limit = 500;
                for ($offset = 0; $offset < $totalRows; $offset += $limit) {
                    $rows = $builder->get($limit, $offset)->getResultArray();
                    if (!empty($rows)) {
                        $fields = array_keys($rows[0]);
                        $escapedFields = array_map(function($f) {
                            return $this->db->escapeIdentifiers($f);
                        }, $fields);

                        fwrite($handle, "INSERT INTO " . $this->db->escapeIdentifiers($table) . " (");
                        fwrite($handle, implode(', ', $escapedFields) . ") VALUES\n");

                        $valueRows = [];
                        foreach ($rows as $rowData) {
                            $valGroup = [];
                            foreach ($rowData as $val) {
                                if ($val === null) {
                                    $valGroup[] = 'NULL';
                                } else {
                                    $valGroup[] = $this->db->escape($val);
                                }
                            }
                            $valueRows[] = "(" . implode(', ', $valGroup) . ")";
                        }
                        fwrite($handle, implode(",\n", $valueRows) . ";\n\n");
                    }
                }
            }
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS = 1;\n");
        fclose($handle);

        $sqlContent = file_get_contents($tempFilePath);
        unlink($tempFilePath);

        return $this->response->setBody($sqlContent);
    }

    public function restoreSql()
    {
        $file = $this->request->getFile('backup_sql');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'File tidak valid atau tidak ditemukan.'
            ])->setStatusCode(400);
        }

        if ($file->getClientExtension() !== 'sql') {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Hanya file dengan ekstensi .sql yang diperbolehkan.'
            ])->setStatusCode(400);
        }

        $tempFile = $file->getTempName();
        $handle = fopen($tempFile, "r");

        if (!$handle) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Gagal membaca file backup.'
            ])->setStatusCode(500);
        }

        try {
            $this->db->disableForeignKeyChecks();

            $query = '';
            $inString = false;
            $stringChar = '';

            while (($line = fgets($handle)) !== false) {
                $lineLen = strlen($line);
                for ($i = 0; $i < $lineLen; $i++) {
                    $char = $line[$i];

                    // Handle quotes/strings to avoid false semicolon matches
                    if (($char === "'" || $char === '"') && ($i === 0 || $line[$i-1] !== '\\')) {
                        if (!$inString) {
                            $inString = true;
                            $stringChar = $char;
                        } elseif ($char === $stringChar) {
                            $inString = false;
                        }
                    }

                    // Handle comments -- or # (only if not inside a string)
                    if (!$inString) {
                        if ($char === '-' && $i < $lineLen - 1 && $line[$i+1] === '-') {
                            break; // skip rest of line
                        }
                        if ($char === '#') {
                            break; // skip rest of line
                        }
                    }

                    $query .= $char;

                    // If semicolon and not in a string, execute query
                    if ($char === ';' && !$inString) {
                        $trimmedQuery = trim($query);
                        if (!empty($trimmedQuery)) {
                            $this->db->query($trimmedQuery);
                        }
                        $query = '';
                    }
                }
            }

            fclose($handle);
            $this->db->enableForeignKeyChecks();

            return $this->response->setJSON([
                'status'  => true,
                'message' => 'Database berhasil direstore dari file SQL.'
            ]);
        } catch (\Throwable $e) {
            if ($handle) {
                fclose($handle);
            }
            $this->db->enableForeignKeyChecks();
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Restore gagal: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function deleteTables()
    {
        $input = $this->request->getJSON(true);
        $tables = $input['tables'] ?? [];

        if (empty($tables)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Tidak ada tabel yang dipilih.'
            ]);
        }

        try {
            // Nonaktifkan foreign key checks sementara
            $this->db->disableForeignKeyChecks();

            // Urutkan agar tabel anak dihapus dulu
            $priorityOrder = [
                'jawaban',
                'hasil_ujian',
                'soal_opsi',
                'soal',
                'topik_soal',
                'banksoal',
                'ujian',
                'peserta',
                'kelas',
                'jurusan',
                'tingkat',
                'agama',
            ];

            // Urutkan tabel berdasarkan prioritas
            usort($tables, function ($a, $b) use ($priorityOrder) {
                $aIndex = array_search($a, $priorityOrder);
                $bIndex = array_search($b, $priorityOrder);
                return ($aIndex !== false ? $aIndex : PHP_INT_MAX) - ($bIndex !== false ? $bIndex : PHP_INT_MAX);
            });

            // Truncate tiap tabel
            foreach ($tables as $table) {
                $this->db->table($table)->truncate();
            }

            $this->db->enableForeignKeyChecks();

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Data berhasil dihapus dari tabel terpilih.'
            ]);
        } catch (\Throwable $e) {
            $this->db->enableForeignKeyChecks(); // pastikan tetap diaktifkan walau error
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
    }
}
