<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class AcademicSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Clear existing values to prevent duplicate key/foreign key constraints
        $db->query('SET FOREIGN_KEY_CHECKS = 0;');
        $db->table('kelas')->truncate();
        $db->table('peserta')->truncate();
        $db->table('mata_pelajaran')->truncate();
        $db->table('users')->truncate();
        $db->table('bank_soal')->truncate();
        $db->table('jadwal_pelajaran')->truncate();
        $db->table('tugas')->truncate();
        $db->table('tugas_jawaban')->truncate();
        $db->table('absensi')->truncate();
        $db->table('rapor_nilai')->truncate();
        $db->table('keuangan_spp')->truncate();
        $db->table('keuangan_jurnal')->truncate();
        $db->table('kesiswaan_prestasi')->truncate();
        $db->table('kesiswaan_pelanggaran')->truncate();
        $db->table('ppdb_pendaftar')->truncate();
        $db->table('inventaris_barang')->truncate();
        $db->table('ekstrakurikuler')->truncate();
        $db->query('SET FOREIGN_KEY_CHECKS = 1;');

        // 2. Seed Mata Pelajaran
        $subjectsData = [
            ['kode' => 'MAPEL-MTK', 'nama' => 'Matematika Wajib'],
            ['kode' => 'MAPEL-ING', 'nama' => 'Bahasa Inggris'],
            ['kode' => 'MAPEL-FIS', 'nama' => 'Fisika Eksperimen'],
            ['kode' => 'MAPEL-KIM', 'nama' => 'Kimia Teori'],
            ['kode' => 'MAPEL-IND', 'nama' => 'Bahasa Indonesia'],
        ];

        $subjectMap = [];
        foreach ($subjectsData as $s) {
            $id = Uuid::uuid4()->toString();
            $db->table('mata_pelajaran')->insert([
                'id' => $id,
                'kode' => $s['kode'],
                'nama' => $s['nama'],
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $subjectMap[$s['nama']] = $id;
        }

        // 3. Seed Users (Admin, Proctor, and Teachers/Wali Kelas)
        $adminId = Uuid::uuid4()->toString();
        $pengawasId = Uuid::uuid4()->toString();

        $users = [
            [
                'id' => $adminId,
                'username' => 'admin',
                'email' => 'admin@example.com',
                'full_name' => 'Administrator Utama',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'is_active' => 1,
                'roles' => 'admin,superuser',
                'mata_pelajaran_id' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => $pengawasId,
                'username' => 'pengawas',
                'email' => 'pengawas@example.com',
                'full_name' => 'Pengawas Ujian',
                'password' => password_hash('pengawas123', PASSWORD_DEFAULT),
                'is_active' => 1,
                'roles' => 'pengawas',
                'mata_pelajaran_id' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $teachers = [
            [
                'username' => 'guru_mtk',
                'email' => 'guru_mtk@example.com',
                'full_name' => 'Ibu Herlina, S.Pd',
                'subject_name' => 'Matematika Wajib',
                'roles' => 'guru,wali_kelas', // Can act as homeroom teacher
            ],
            [
                'username' => 'guru_ing',
                'email' => 'guru_ing@example.com',
                'full_name' => 'Bpk. Rudi Hartono',
                'subject_name' => 'Bahasa Inggris',
                'roles' => 'guru,wali_kelas', // Can act as homeroom teacher
            ],
            [
                'username' => 'guru_fis',
                'email' => 'guru_fis@example.com',
                'full_name' => 'Ibu Citra Lestari',
                'subject_name' => 'Fisika Eksperimen',
                'roles' => 'guru',
            ],
            [
                'username' => 'guru_kim',
                'email' => 'guru_kim@example.com',
                'full_name' => 'Bpk. Bambang Pamungkas',
                'subject_name' => 'Kimia Teori',
                'roles' => 'guru',
            ],
            [
                'username' => 'guru_ind',
                'email' => 'guru_ind@example.com',
                'full_name' => 'Ibu Maria Ulfa',
                'subject_name' => 'Bahasa Indonesia',
                'roles' => 'guru',
            ],
        ];

        $teacherMap = []; // map teacher name/full_name to user info
        foreach ($teachers as $t) {
            $id = Uuid::uuid4()->toString();
            $mapelId = $subjectMap[$t['subject_name']] ?? null;

            $users[] = [
                'id' => $id,
                'username' => $t['username'],
                'email' => $t['email'],
                'full_name' => $t['full_name'],
                'password' => password_hash('guru123', PASSWORD_DEFAULT),
                'is_active' => 1,
                'roles' => $t['roles'],
                'mata_pelajaran_id' => $mapelId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $teacherMap[$t['full_name']] = [
                'user_id' => $id,
                'mata_pelajaran_id' => $mapelId
            ];
        }

        $db->table('users')->insertBatch($users);

        // 4. Seed Classes (Kelas)
        // Set Ibu Herlina, S.Pd (guru_mtk) as Wali Kelas of X-A
        $waliKelasId = $teacherMap['Ibu Herlina, S.Pd']['user_id'];
        $kelasId = Uuid::uuid4()->toString();
        $db->table('kelas')->insert([
            'id' => $kelasId,
            'nama' => 'X-A',
            'wali_kelas_id' => $waliKelasId,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $kelas = [['id' => $kelasId, 'nama' => 'X-A']];

        // 5. Seed Students (Peserta)
        $pesertaId = Uuid::uuid4()->toString();
        $db->table('peserta')->insert([
            'id' => $pesertaId,
            'nama' => 'Ahmad Fauzi',
            'nisn' => '1234567890',
            'nis' => '20261001',
            'nik' => '3273012345670001',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2010-04-12',
            'jenis_kelamin' => 'L',
            'telepon' => '081234567890',
            'email' => 'ahmad.fauzi@sch.id',
            'alamat' => 'Jl. Merdeka No. 45',
            'rt' => '003',
            'rw' => '007',
            'dusun' => 'Mekarsari',
            'kelurahan' => 'Babakan Ciamis',
            'kecamatan' => 'Sumur Bandung',
            'kode_pos' => '40117',
            'jenis_tinggal' => 'Bersama Orang Tua',
            'alat_transportasi' => 'Sepeda Motor',
            
            // Ayah
            'nama_ayah' => 'Budi Fauzi',
            'nik_ayah' => '3273010101700002',
            'tahun_lahir_ayah' => 1975,
            'pendidikan_ayah' => 'S1 Teknik',
            'pekerjaan_ayah' => 'Karyawan Swasta',
            'penghasilan_ayah' => 'Rp 5.000.000 - Rp 10.000.000',

            // Ibu
            'nama_ibu' => 'Siti Aminah',
            'nik_ibu' => '3273010101800003',
            'tahun_lahir_ibu' => 1980,
            'pendidikan_ibu' => 'SMA/Sederajat',
            'pekerjaan_ibu' => 'Ibu Rumah Tangga',
            'penghasilan_ibu' => 'Kurang dari Rp 1.000.000',

            'username' => 'ahmadf',
            'password' => base64_encode('ahmadf123cbthassina'), // simple base64 mock
            'kelas_id' => $kelasId,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $peserta = [['id' => $pesertaId, 'nama' => 'Ahmad Fauzi', 'kelas_id' => $kelasId]];

        // 6. Seed Bank Soal
        $bankSoalCodes = [
            'Matematika Wajib' => 'SOAL-MTK',
            'Bahasa Inggris'   => 'SOAL-ING',
            'Fisika Eksperimen'=> 'SOAL-FIS',
            'Kimia Teori'      => 'SOAL-KIM',
            'Bahasa Indonesia' => 'SOAL-IND',
        ];

        foreach ($teacherMap as $fullName => $info) {
            $subjectName = array_search($info['mata_pelajaran_id'], $subjectMap);
            $kode = $bankSoalCodes[$subjectName] ?? 'SOAL-' . strtoupper(substr($fullName, 0, 3));
            
            $db->table('bank_soal')->insert([
                'id' => Uuid::uuid4()->toString(),
                'kode' => $kode,
                'nama' => 'Bank Soal ' . $subjectName . ' Kelas X',
                'deskripsi' => 'Kumpulan soal pelajaran ' . $subjectName . ' untuk latihan ujian harian dan semester.',
                'created_by' => $info['user_id'],
                'mata_pelajaran_id' => $info['mata_pelajaran_id'],
                'is_active' => 1,
                'is_public' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        // 7. Seed Jadwal Pelajaran (linked to guru_id & guru_nama)
        $mapels = [
            ['nama' => 'Matematika Wajib', 'guru' => 'Ibu Herlina, S.Pd', 'ruang' => 'Ruang A-102'],
            ['nama' => 'Bahasa Inggris', 'guru' => 'Bpk. Rudi Hartono', 'ruang' => 'Ruang A-102'],
            ['nama' => 'Fisika Eksperimen', 'guru' => 'Ibu Citra Lestari', 'ruang' => 'Lab Fisika'],
            ['nama' => 'Kimia Teori', 'guru' => 'Bpk. Bambang Pamungkas', 'ruang' => 'Ruang A-102'],
            ['nama' => 'Bahasa Indonesia', 'guru' => 'Ibu Maria Ulfa', 'ruang' => 'Ruang A-102'],
        ];

        $hari = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];

        foreach ($kelas as $k) {
            foreach ($hari as $index => $h) {
                $mapel = $mapels[$index % count($mapels)];
                $mapelId = $subjectMap[$mapel['nama']] ?? null;
                $guruId = $teacherMap[$mapel['guru']]['user_id'] ?? null;

                $db->table('jadwal_pelajaran')->insert([
                    'id' => Uuid::uuid4()->toString(),
                    'kelas_id' => $k['id'],
                    'mata_pelajaran_id' => $mapelId,
                    'hari' => $h,
                    'waktu_mulai' => '07:30:00',
                    'waktu_selesai' => '09:00:00',
                    'guru_id' => $guruId,
                    'guru_nama' => $mapel['guru'],
                    'ruangan' => $mapel['ruang'],
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        // 8. Seed Tugas & PR
        $tugasList = [
            ['judul' => 'PR Fisika: Rangkuman Hukum Newton', 'mapel' => 'Fisika Eksperimen', 'deskripsi' => 'Rangkumlah Bab 3 Dinamika Partikel mengenai Hukum Newton 1, 2, dan 3. Tulis tangan di buku latihan lalu kumpulkan.'],
            ['judul' => 'Laporan Praktikum Asam Basa', 'mapel' => 'Kimia Teori', 'deskripsi' => 'Unggah laporan praktikum indikator alami asam basa dalam format PDF.'],
            ['judul' => 'Latihan Soal Eksponen', 'mapel' => 'Matematika Wajib', 'deskripsi' => 'Kerjakan Latihan 2.1 Halaman 42 buku cetak Matematika Wajib.'],
        ];

        foreach ($kelas as $k) {
            foreach ($tugasList as $index => $t) {
                $tugasId = Uuid::uuid4()->toString();
                $mapelId = $subjectMap[$t['mapel']] ?? null;

                $db->table('tugas')->insert([
                    'id' => $tugasId,
                    'kelas_id' => $k['id'],
                    'judul' => $t['judul'],
                    'deskripsi' => $t['deskripsi'],
                    'tenggat_waktu' => date('Y-m-d H:i:s', strtotime('+3 days')),
                    'mata_pelajaran_id' => $mapelId,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                // Create a mock submission for the first student on the third task
                if ($index === 2 && !empty($peserta)) {
                    $student = $peserta[0];
                    if ($student['kelas_id'] === $k['id']) {
                        $db->table('tugas_jawaban')->insert([
                            'id' => Uuid::uuid4()->toString(),
                            'tugas_id' => $tugasId,
                            'peserta_id' => $student['id'],
                            'file_path' => 'uploads/tugas/dummy_latihan.pdf',
                            'catatan_guru' => 'Sangat bagus, pengerjaan lengkap dan rapi.',
                            'nilai' => 95,
                            'tanggal_kirim' => date('Y-m-d H:i:s', strtotime('-1 day')),
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }
        }

        // 9. Seed Rapor Nilai
        $subjects = ['Matematika Wajib', 'Fisika Eksperimen', 'Kimia Teori', 'Bahasa Indonesia', 'Bahasa Inggris'];
        foreach ($peserta as $p) {
            foreach ($subjects as $sub) {
                $nilai = rand(70, 95);
                $grade = 'B';
                if ($nilai >= 85) {
                    $grade = 'A';
                } elseif ($nilai >= 75) {
                    $grade = 'B';
                } elseif ($nilai >= 60) {
                    $grade = 'C';
                }
                
                $mapelId = $subjectMap[$sub] ?? null;

                $db->table('rapor_nilai')->insert([
                    'id' => Uuid::uuid4()->toString(),
                    'peserta_id' => $p['id'],
                    'mata_pelajaran_id' => $mapelId,
                    'nilai' => $nilai,
                    'grade' => $grade,
                    'semester' => 'Genap',
                    'tahun_ajaran' => '2025/2026',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        // 10. Seed Keuangan SPP
        $bulans = ['Mei', 'Juni'];
        foreach ($peserta as $p) {
            foreach ($bulans as $index => $b) {
                $status = ($b === 'Mei') ? 'lunas' : 'belum_bayar';
                $metode = ($status === 'lunas') ? 'tunai' : null;
                $tglBayar = ($status === 'lunas') ? date('Y-m-d H:i:s', strtotime('-2 weeks')) : null;
                
                $invoiceNum = 'INV/SPP/2026/' . strtoupper(substr(md5($p['id'] . $b), 0, 8));

                $db->table('keuangan_spp')->insert([
                    'id' => Uuid::uuid4()->toString(),
                    'peserta_id' => $p['id'],
                    'bulan' => $b,
                    'nominal' => 150000,
                    'status_bayar' => $status,
                    'metode_bayar' => $metode,
                    'tanggal_bayar' => $tglBayar,
                    'invoice_number' => $invoiceNum,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        // 11. Seed Keuangan Jurnal (Pemasukan & Pengeluaran)
        $jurnals = [
            [
                'id' => Uuid::uuid4()->toString(),
                'tanggal' => date('Y-m-d', strtotime('-5 days')),
                'keterangan' => 'Penerimaan SPP Kelas X Semester Genap',
                'tipe' => 'debit',
                'nominal' => 3000000.00,
                'kategori' => 'SPP',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'tanggal' => date('Y-m-d', strtotime('-10 days')),
                'keterangan' => 'Penerimaan Dana BOS Tahap I',
                'tipe' => 'debit',
                'nominal' => 25000000.00,
                'kategori' => 'Dana BOS',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'tanggal' => date('Y-m-d', strtotime('-3 days')),
                'keterangan' => 'Pembelian Kertas HVS & ATK Ujian',
                'tipe' => 'kredit',
                'nominal' => 750000.00,
                'kategori' => 'ATK',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'tanggal' => date('Y-m-d', strtotime('-1 day')),
                'keterangan' => 'Gaji Guru & Staff Bulanan',
                'tipe' => 'kredit',
                'nominal' => 12000000.00,
                'kategori' => 'Gaji',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'tanggal' => date('Y-m-d', strtotime('-2 days')),
                'keterangan' => 'Pembelian Switch Hub Lab Komputer',
                'tipe' => 'kredit',
                'nominal' => 1200000.00,
                'kategori' => 'Inventaris',
                'created_at' => date('Y-m-d H:i:s'),
            ]
        ];
        $db->table('keuangan_jurnal')->insertBatch($jurnals);

        // 12. Seed Kesiswaan Prestasi
        if ($pesertaId) {
            $db->table('kesiswaan_prestasi')->insert([
                'id' => Uuid::uuid4()->toString(),
                'peserta_id' => $pesertaId,
                'nama_prestasi' => 'Juara 2 Olimpiade Fisika Nasional',
                'tingkat' => 'Nasional',
                'kategori' => 'Akademik',
                'tanggal' => date('Y-m-d', strtotime('-15 days')),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        // 13. Seed Kesiswaan Pelanggaran
        if ($pesertaId) {
            $db->table('kesiswaan_pelanggaran')->insert([
                'id' => Uuid::uuid4()->toString(),
                'peserta_id' => $pesertaId,
                'nama_pelanggaran' => 'Tidak memakai seragam lengkap saat upacara hari Senin',
                'kategori' => 'Ringan',
                'point' => 5,
                'tanggal' => date('Y-m-d', strtotime('-8 days')),
                'tindakan' => 'Teguran lisan & pembinaan oleh wali kelas',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        // 14. Seed PPDB Pendaftar
        $pendaftars = [
            [
                'id' => Uuid::uuid4()->toString(),
                'nomor_daftar' => 'PPDB-' . date('Ymd') . '-001',
                'nama' => 'Budi Santoso',
                'nisn' => '1234509876',
                'email' => 'budi@gmail.com',
                'telepon' => '08123456789',
                'sekolah_asal' => 'SMP Negeri 2 Bandung',
                'status' => 'proses',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'nomor_daftar' => 'PPDB-' . date('Ymd') . '-002',
                'nama' => 'Siti Rahma',
                'nisn' => '9876543210',
                'email' => 'siti@gmail.com',
                'telepon' => '08129876543',
                'sekolah_asal' => 'SMP Swasta Al-Ikhlas',
                'status' => 'diterima',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'nomor_daftar' => 'PPDB-' . date('Ymd') . '-003',
                'nama' => 'Rian Hidayat',
                'nisn' => '5432109876',
                'email' => 'rian@gmail.com',
                'telepon' => '08134567890',
                'sekolah_asal' => 'SMP Negeri 5 Bogor',
                'status' => 'menunggu',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
        $db->table('ppdb_pendaftar')->insertBatch($pendaftars);

        // 15. Seed Inventaris Barang
        $barangs = [
            [
                'id' => Uuid::uuid4()->toString(),
                'nama_barang' => 'Laptop ASUS Core i5',
                'kode_barang' => 'INV-LAB-001',
                'jumlah' => 10,
                'kondisi' => 'baik',
                'lokasi' => 'Laboratorium Komputer',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'nama_barang' => 'Proyektor Epson',
                'kode_barang' => 'INV-LAB-002',
                'jumlah' => 2,
                'kondisi' => 'rusak',
                'lokasi' => 'Laboratorium Komputer',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'nama_barang' => 'Air Conditioner Sharp',
                'kode_barang' => 'INV-GURU-001',
                'jumlah' => 2,
                'kondisi' => 'baik',
                'lokasi' => 'Ruang Guru',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
        $db->table('inventaris_barang')->insertBatch($barangs);

        // 16. Seed Ekstrakurikuler
        $ekstras = [
            [
                'id' => Uuid::uuid4()->toString(),
                'nama_ekstra' => 'Pramuka',
                'pembina_nama' => 'Kak Dedi Setiawan',
                'jadwal_hari' => 'Sabtu',
                'waktu' => '14:00:00',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'nama_ekstra' => 'Futsal',
                'pembina_nama' => 'Coach Roni',
                'jadwal_hari' => 'Rabu',
                'waktu' => '16:00:00',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'nama_ekstra' => 'Paskibra',
                'pembina_nama' => 'Ibu Lilik S.',
                'jadwal_hari' => 'Jumat',
                'waktu' => '15:30:00',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
        $db->table('ekstrakurikuler')->insertBatch($ekstras);

        echo "Academic database seeding completed successfully!\n";
    }
}
