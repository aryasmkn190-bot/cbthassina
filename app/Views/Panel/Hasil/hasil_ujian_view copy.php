<?= $this->extend('Layout/main') ?>
<?= $this->section('content') ?>
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">
            <div class="widget widget-three">
                <div class="widget-heading d-flex justify-content-between align-items-start flex-wrap">
                    <div>
                        <h5 class="mb-1">Hasil Ujian</h5>
                        <div class="text-muted small">
                            <div><strong><?= esc($ujian['nama_ujian']) ?></strong></div>
                            <div><strong>Durasi <?= esc($ujian['durasi_ujian']) ?> menit</strong></div>
                            <div><strong><?= date('d M Y, H:i', strtotime($ujian['waktu_mulai'])) ?></strong></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end align-items-center gap-3 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" role="switch" id="autoRefreshSwitch">
                            </div>
                            <label class="ms-2 mb-0 small" for="autoRefreshSwitch" id="autoRefreshLabel">Auto Refresh</label>
                        </div>

                        <!-- Tombol Unduh Excel -->
                        <a href="<?= base_url('panel/hasil-ujian/exportskoring/' . $ujianid) ?>" class="btn btn-success d-flex align-items-center gap-2" id="unduhExcelBtn">
                            <i data-feather="download"></i> Excel
                        </a>

                        <!-- Tombol Refresh -->
                        <button type="button" id="kirimjawaban" class="btn btn-outline-success d-flex align-items-center">
                            <i data-feather="upload"></i>
                        </button>
                        <!-- Tombol Refresh -->
                        <button type="button" id="refreshtable" class="btn btn-outline-secondary d-flex align-items-center">
                            <i data-feather="refresh-cw"></i>
                        </button>
                    </div>

                </div>

                <div class="widget-content">
                    <div class="table-responsive">
                        <table class="table" id="hasilUjianTable" width="100%">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>NISN</th>
                                    <th>Nama Peserta</th>
                                    <th>Kelas</th>
                                    <th>Jawaban</th>
                                    <th>Nilai</th>
                                    <th>Lama Ujian</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalDetailJawaban" tabindex="-1" aria-labelledby="modalDetailJawabanLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailJawabanLabel">Detail Jawaban</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="detailJawabanKonten">
                <p class="text-center text-muted">Memuat jawaban...</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-outline-secondary" onclick="printJawaban()">Print</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('pagejs'); ?>
<script>
    $(document).ready(function() {
        const ujianId = '<?= $ujianid ?>';

        $('#hasilUjianTable').DataTable({
            ajax: {
                url: '<?= base_url('panel/hasil-ujian/get/') ?>' + ujianId,
                type: 'GET',
                dataSrc: 'data'
            },
            columns: [{
                    data: null,
                    className: 'text-center',
                    render: (data, type, row, meta) => meta.row + 1
                },
                {
                    data: 'nisn'
                },
                {
                    data: 'nama_peserta'
                },
                {
                    data: 'nama_kelas'
                },
                {
                    data: null,
                    className: 'text-center',
                    render: row => `
                                <div style="line-height: 1.4;">
                                    <div><strong>✅ </strong> 
                                        <span class="text-primary fw-bold">${row.soal_benar}</span> / 
                                        <strong> ❌ </strong> 
                                        <span class="text-danger">${row.soal_salah}</span>
                                    </div>
                                    <div><strong>🎯 Poin:</strong> 
                                        <span class="text-success fw-bold">${row.poin_benar}</span> / 
                                        <span class="text-muted">${row.poin_maksimal}</span>
                                    </div>
                                </div>
                            `
                },


                {
                    data: null,
                    className: 'text-center',
                    render: row => {
                        return row.nilai_total ? parseFloat(row.nilai_total).toFixed(2) : '0.00';
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    render: row => {
                        if (!row.waktu_mulai || !row.waktu_selesai) return '-';
                        const mulai = new Date(row.waktu_mulai);
                        const selesai = new Date(row.waktu_selesai);
                        const diffMs = selesai - mulai;
                        const menit = Math.floor(diffMs / 60000);
                        const detik = Math.floor((diffMs % 60000) / 1000);
                        return `${menit}m ${detik}s`;
                    }
                },
                {
                    data: 'status',
                    className: 'text-center',
                    render: function(status) {
                        if (status === 'belum_mulai') {
                            return '<span class="badge bg-danger">Belum Mulai</span>';
                        } else if (status === 'sedang_ujian') {
                            return `
                                    <span class="badge bg-warning text-dark d-inline-flex align-items-center gap-1">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Sedang Dikerjakan
                                    </span>
                                `;
                        } else if (status === 'selesai') {
                            return '<span class="badge bg-success">Selesai</span>';
                        } else {
                            return '<span class="badge bg-secondary">Tidak Diketahui</span>';
                        }
                    }
                },

                {
                    data: null,
                    className: 'text-center',
                    render: row => {
                        let tombol = `
                                        <div class="d-flex flex-row gap-1 align-items-center">
                                            <button class="btn btn-sm btn-outline-warning ulang-ujian-btn" data-id="${row.id}" title="Ulang Ujian">
                                                <i data-feather="rotate-ccw"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-info lihat-jawaban-btn" data-id="${row.id}" title="Lihat Jawaban">
                                                <i data-feather="eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger reset-device-btn" data-id="${row.id}" title="Buka Kunci Perangkat">
                                                <i data-feather="unlock"></i>
                                            </button>
                                    `;

                        if (row.status === 'sedang_ujian') {
                            tombol += `
                                        <button class="btn btn-sm btn-outline-success selesai-ujian-btn" data-id="${row.ujian_id}" data-idpeserta="${row.peserta_id}" title="Selesaikan Ujian">
                                            <i data-feather="check-circle"></i>
                                        </button>
                                    `;
                        }

                        tombol += `</div>`;
                        return tombol;
                    }
                }


            ],

            "dom": "<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>" +
                "<'table-responsive'tr>" +
                "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
            "oLanguage": {
                "oPaginate": {
                    "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                    "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
                },
                "sInfo": "Showing page _PAGE_ of _PAGES_",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "Search...",
                "sLengthMenu": "Results :  _MENU_",
            },
            drawCallback: function() {
                feather.replace();
            }
        });
        $('#hasilUjianTable').on('click', '.reset-device-btn', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Buka Kunci Perangkat?',
                text: "Device akan direset dan peserta bisa login ulang.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, reset!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('panel/hasil-ujian/reset-device') ?>',
                        type: 'POST',
                        data: {
                            id: id
                        },
                        dataType: 'json',
                        success: function(res) {
                            if (res.status) {
                                Snackbar.show({
                                    text: res.message,
                                    pos: 'top-center',
                                    backgroundColor: '#28a745'
                                });
                                $('#hasilUjianTable').DataTable().ajax.reload(null, false);
                            } else {
                                Snackbar.show({
                                    text: res.message,
                                    pos: 'top-center',
                                    backgroundColor: '#e7515a'
                                });
                            }
                        },
                        error: function() {
                            Snackbar.show({
                                text: 'Terjadi kesalahan server',
                                pos: 'top-center',
                                backgroundColor: '#e7515a'
                            });
                        }
                    });
                }
            });
        });

        $('#refreshtable').on('click', function() {
            $('#hasilUjianTable').DataTable().ajax.reload(null, false); // false = tetap di halaman aktif
        });
        let autoRefreshInterval = null;
        let countdownInterval = null;
        const intervalDetik = 10;

        $('#autoRefreshSwitch').on('change', function() {
            const $label = $('#autoRefreshLabel');

            if ($(this).is(':checked')) {
                let count = intervalDetik;

                // Tampilkan awal label
                $label.text(`Refresh dalam ${count}s`);

                // Interval refresh DataTable
                autoRefreshInterval = setInterval(() => {
                    $('#hasilUjianTable').DataTable().ajax.reload(null, false);
                    count = intervalDetik; // reset countdown setelah reload
                }, intervalDetik * 1000);

                // Interval untuk countdown label
                countdownInterval = setInterval(() => {
                    count--;
                    $label.text(`Refresh dalam ${count}s`);
                }, 1000);

            } else {
                clearInterval(autoRefreshInterval);
                clearInterval(countdownInterval);
                $label.text('Auto Refresh');
            }
        });
        $(document).on('click', '.lihat-jawaban-btn', function() {
            const hasilUjianId = $(this).data('id');
            $('#detailJawabanKonten').html('<p class="text-muted text-center">Memuat data...</p>');


            $.getJSON(`<?= base_url('panel/hasil-ujian/jawaban') ?>/${hasilUjianId}`, function(res) {
                if (res.status) {
                    console.log("okkk")
                    renderDetailJawaban(res.data);


                } else {
                    $('#detailJawabanKonten').html(`<div class="alert alert-warning">${res.message}</div>`);
                }
            }).fail(() => {
                $('#detailJawabanKonten').html('<div class="alert alert-danger">Gagal memuat data.</div>');
            });
        });


        // Tombol Ulang Ujian
        // Ulang Ujian
        $('#hasilUjianTable').on('click', '.ulang-ujian-btn', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Ulangi Ujian?',
                text: 'Semua jawaban peserta akan dihapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ulangi',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#e3342f'
            }).then(result => {
                if (result.isConfirmed) {
                    $.post(`<?= base_url('panel/hasil-ujian/ulang/') ?>${id}`, function(res) {
                        if (res.status) {
                            $('#hasilUjianTable').DataTable().ajax.reload();
                            Snackbar.show({
                                text: res.message,
                                pos: 'top-center',
                                backgroundColor: '#ffc107'
                            });
                        } else {
                            Snackbar.show({
                                text: res.message,
                                pos: 'top-center',
                                backgroundColor: '#dc3545'
                            });
                        }
                    });
                }
            });
        });

        // Selesai Ujian
        $('#hasilUjianTable').on('click', '.selesai-ujian-btn', function() {
            const id = $(this).data('id');
            const peserta_id = $(this).data('idpeserta');
            Swal.fire({
                title: 'Selesaikan Ujian?',
                text: 'Peserta tidak bisa mengerjakan lagi.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Selesaikan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#38c172'
            }).then(result => {
                if (result.isConfirmed) {
                    $.post(`<?= base_url('panel/hasil-ujian/selesai/') ?>${id}`, {
                        peserta_id
                    }, function(res) {
                        if (res.status) {
                            $('#hasilUjianTable').DataTable().ajax.reload();
                            Snackbar.show({
                                text: res.message,
                                pos: 'top-center',
                                backgroundColor: '#28a745'
                            });
                        } else {
                            Snackbar.show({
                                text: res.message,
                                pos: 'top-center',
                                backgroundColor: '#dc3545'
                            });
                        }
                    });
                }
            });
        });

        $('#kirimjawaban').on('click', function() {
            const token = localStorage.getItem('syncToken');
            const url = localStorage.getItem('syncServerUrl');
            const ujianId = '<?= $ujianid ?>';

            if (!token || !url) {
                Swal.fire('Gagal', 'Token atau URL belum tersimpan. Silakan cek koneksi dulu.', 'error');
                return;
            }

            Swal.fire({
                title: 'Kirim Jawaban?',
                text: 'Data hasil ujian selesai akan dikirim ke server pusat.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Kirim Sekarang',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#0d6efd'
            }).then(result => {
                if (result.isConfirmed) {
                    $('#kirimjawaban').prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i> Mengirim...');

                    $.ajax({
                        url: `<?= base_url('panel/hasil-ujian/sinkronisasi') ?>`,
                        type: 'POST',
                        data: {
                            token,
                            url,
                            ujian_id: ujianId
                        },
                        success: function(res) {
                            if (res.status) {
                                Swal.fire('Sukses', res.message, 'success');
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Gagal', 'Terjadi kesalahan saat mengirim data.', 'error');
                        },
                        complete: function() {
                            $('#kirimjawaban').prop('disabled', false).html('<i data-feather="upload"></i>');
                            feather.replace();
                        }
                    });
                }
            });
        });


    });

    function renderDetailJawaban(data) {
        const {
            peserta,
            soalList,
            jawaban,
            opsiList
        } = data;

        let html = `
    <div class="mb-4 p-3 rounded bg-light border shadow-sm">
        <div class="d-flex justify-content-between align-items-start">
            <div class="d-flex justify-content-start flex-wrap gap-4 small text-secondary">
                <div><span class="fw-semibold text-dark">Nama:</span> ${peserta?.nama ?? '-'}</div>
                <div><span class="fw-semibold text-dark">NISN:</span> ${peserta?.nisn ?? '-'}</div>
                <div><span class="fw-semibold text-dark">Kelas:</span> ${peserta?.nama_kelas ?? '-'}</div>
            </div>
            <div class="text-end">
                <div class="fw-bold text-primary" style="font-size: 1.2rem;">
                    Poin: ${data.hasil.poin_benar} / ${data.hasil.poin_maksimal}
                </div>
                <div class="small text-muted">Nilai Akhir: ${data.hasil.nilai_total}</div>
            </div>
        </div>
    </div>`;


        soalList.forEach((soal, i) => {
            const nomor = i + 1;
            const jwb = jawaban[soal.id] ?? null;
            const opsi = opsiList[soal.id] ?? [];
            const poin = jwb?.poin ?? 0;
            const benar = jwb?.is_benar ?? false;

            let status = `<span class="badge bg-danger">Belum Dijawab</span>`;
            if (jwb) {
                status = benar ?
                    `<span class="badge bg-success">Benar</span>` :
                    `<span class="badge bg-warning">Salah</span>`;
            }

            html += `
        <div class="mb-4 border-bottom pb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Soal ${nomor}</h6>
                <div class="d-flex gap-2 align-items-center">
                    ${status}
                    <span class="badge bg-secondary">+${poin} poin</span>
                </div>
            </div>
            <div class="mb-2">${soal.pertanyaan}</div>
            ${renderOpsi(soal, opsi, jwb)}
        </div>`;
        });

        $('#detailJawabanKonten').html(html);
        $('#modalDetailJawaban').modal('show');
    }

    function renderOpsi(soal, opsiList, jawaban) {
        const jenis = soal.jenis_soal;
        if (!jawaban) return `<em class="text-muted fst-italic">Belum menjawab</em>`;

        const highlight = (dipilih, benar) => {
            if (dipilih && benar) return 'border-success bg-success-subtle';
            if (dipilih && !benar) return 'border-danger bg-danger-subtle';
            if (!dipilih && benar) return 'border-success bg-success-subtle';
            return 'bg-light';
        };

        if (['pg', 'mpg'].includes(jenis)) {
            const selected = jenis === 'pg' ? [jawaban.value] : (jawaban.values ?? []);
            return `
        <div class="list-group shadow-sm rounded-2 overflow-hidden">
            ${opsiList.map(op => {
                const dipilih = selected.includes(op.label);
                const benar = op.is_true == '1';
                return `
                <div class="list-group-item d-flex justify-content-between align-items-center border ${highlight(dipilih, benar)}">
                    <div class="d-flex gap-2">
                        <strong>${op.label}.</strong><span>${op.teks}</span>
                    </div>
                    <span class="badge bg-dark-subtle text-dark">Bobot: ${op.bobot}</span>
                </div>`;
            }).join('')}
        </div>`;
        }

        if (jenis === 'benar_salah') {
            return `
            <div class="list-group shadow-sm rounded-2 overflow-hidden">
                ${opsiList.map(op => {
                    const jwbPeserta = (jawaban[op.label] ?? '').toLowerCase();
                    const isTrue = op.is_true === '1'; // perbaikan tipe data
                    const kunci = isTrue ? 'benar' : 'salah';
                    const benar = jwbPeserta === kunci;

                    return `
                    <div class="list-group-item d-flex justify-content-between align-items-center border ${highlight(true, benar)}">
                        <div>
                            <strong>${op.label}.</strong> <span>${op.teks}</span>
                            <div class="small text-muted">
                                Jawaban: <strong>${jwbPeserta || '-'}</strong> | Kunci: <strong>${kunci}</strong>
                            </div>
                        </div>
                        <span class="badge bg-dark-subtle text-dark">Bobot: ${op.bobot}</span>
                    </div>`;
                }).join('')}
            </div>`;
        }


        if (jenis === 'jodohkan') {
            return `
        <div class="list-group shadow-sm rounded-2 overflow-hidden">
            ${opsiList.map(op => {
                const jwbPeserta = jawaban[op.label] ?? '-';
                const benar = jwbPeserta === op.pasangan;
                return `
                <div class="list-group-item d-flex justify-content-between align-items-center border ${highlight(jwbPeserta !== '-', benar)}">
                    <div class="d-flex gap-2">
                        <strong>${op.label}.</strong><span>${op.teks}</span>
                    </div>
                    <div class="text-end small">
                        <div>Jawab: <strong>${jwbPeserta}</strong></div>
                        <div>⇄ Kunci: ${op.pasangan}</div>
                        <span class="badge bg-dark-subtle text-dark">Bobot: ${op.bobot}</span>
                    </div>
                </div>`;
            }).join('')}
        </div>`;
        }

        if (jenis === 'isian') {
            return `<div class="form-control bg-light">${jawaban.value || '-'}</div>`;
        }

        if (jenis === 'esai') {
            return `<div class="form-control bg-light">${jawaban.text || '-'}</div>`;
        }

        return '';
    }
</script>
<script>
    function printJawaban() {
        const content = document.getElementById('detailJawabanKonten').innerHTML;

        const win = window.open('', '_blank');
        win.document.write(`
        <html>
        <head>
            <title>Print Jawaban</title>
            <link href="<?= base_url() ?>src/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
            <style>
                body { padding: 20px; font-size: 14px; }
                .badge { font-size: 0.75rem; }
                .list-group-item { page-break-inside: avoid; }
            </style>
        </head>
        <body>
            <h4 class="mb-3">Detail Jawaban Peserta</h4>
            ${content}
        </body>
        </html>
    `);
        win.document.close();

        // Tunggu seluruh halaman selesai dimuat, baru print
        win.onload = function() {
            win.focus();
            setTimeout(() => {
                win.print();

                // Fallback untuk menutup jika onafterprint tidak dipicu
                const closeFallback = setTimeout(() => {
                    win.close();
                }, 300);

                win.onafterprint = () => {
                    clearTimeout(closeFallback);
                    win.close();
                };
            }, 300);
        };

    }
</script>

<?= $this->endSection() ?>