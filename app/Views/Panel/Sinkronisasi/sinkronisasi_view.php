<?= $this->extend('Layout/main'); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">🔄 Sinkronisasi Data</h5>
                </div>
                <div class="card-body">

                    <!-- Input URL -->
                    <div class="row justify-content-center mb-3">
                        <div class="col-md-6">
                            <label for="urlInput" class="form-label fw-semibold">URL Server Pusat</label>
                            <input type="text" id="urlInput" class="form-control" placeholder="Contoh: https://pusat.example.com" />
                        </div>
                    </div>

                    <!-- Input Token -->
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <label for="tokenInput" class="form-label fw-semibold">Token Akses</label>
                            <div class="input-group">
                                <input type="text" id="tokenInput" class="form-control" placeholder="Masukkan token..." />
                                <button class="btn btn-outline-primary" type="button" id="cekTokenBtn">
                                    <i data-feather="wifi"></i> Cek Koneksi
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Sinkronisasi -->
                    <div class="row justify-content-center mt-3">
                        <div class="col-auto">
                            <button id="syncBtn" class="btn btn-primary" disabled>
                                <i data-feather="refresh-cw"></i> Sinkronisasi Sekarang
                            </button>
                        </div>
                    </div>

                    <!-- Status Sinkronisasi -->
                    <div class="row mt-4" id="syncStatusBox" style="display: none;">
                        <div class="col-md-8 mx-auto" id="syncStatusContent">
                            <!-- JS akan menambahkan status per tipe data & sub-item -->
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    feather.replace();
    const loadingIcon = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>`;

    // Load token & URL dari localStorage
    const savedToken = localStorage.getItem('syncToken');
    const savedUrl = localStorage.getItem('syncServerUrl');
    if (savedToken) $('#tokenInput').val(savedToken);
    if (savedUrl) $('#urlInput').val(savedUrl);

    // Cek koneksi
    $('#cekTokenBtn').on('click', function() {
        const token = $('#tokenInput').val().trim();
        const url = $('#urlInput').val().trim();
        if (!url || !token) return Swal.fire('Perhatian', 'URL dan Token harus diisi', 'warning');

        $('#cekTokenBtn').prop('disabled', true).html(loadingIcon + 'Mengecek...');

        $.getJSON(`${url}/api/cekkoneksi?token=${token}`, function(res) {
            if (res.success) {
                localStorage.setItem('syncToken', token);
                localStorage.setItem('syncServerUrl', url);

                $.post('<?= base_url('panel/sinkronisasi/simpan_koneksi') ?>', {
                    token,
                    url
                });

                Swal.fire('Terhubung!', 'Server merespons. Anda dapat melanjutkan sinkronisasi.', 'success');
                $('#syncBtn').prop('disabled', false).data('token', token).data('url', url);
            } else {
                Swal.fire('Token Tidak Valid', 'Token salah atau server tidak merespons.', 'error');
            }
        }).fail(function() {
            Swal.fire('Gagal Menghubungi', 'Periksa koneksi atau endpoint server.', 'error');
        }).always(function() {
            $('#cekTokenBtn').prop('disabled', false).html('<i data-feather="wifi"></i> Cek Koneksi');
            feather.replace();
        });
    });

    // Sinkronisasi interaktif
    $('#syncBtn').on('click', function() {
        const token = $(this).data('token');
        const url = $(this).data('url');

        Swal.fire({
            title: 'Pilih Data untuk Sinkronisasi',
            html: `
                <div class="text-start mb-3">
                    <label><input type="checkbox" class="sync-option" value="peserta"> Peserta</label><br>
                    <label><input type="checkbox" class="sync-option" value="banksoal"> Bank Soal</label><br>
                    <label><input type="checkbox" class="sync-option" value="jadwal"> Jadwal Ujian</label>
                </div>
                <div class="alert alert-warning small text-start">
                    <i class="me-1 text-warning" data-feather="alert-triangle"></i>
                    <strong>Perhatian:</strong> Data lokal akan digantikan oleh data dari server.
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Lanjut Sinkronisasi',
            preConfirm: () => {
                const selected = [];
                document.querySelectorAll('.sync-option:checked').forEach(cb => selected.push(cb.value));
                if (selected.length === 0) Swal.showValidationMessage('Pilih setidaknya satu data.');
                return selected;
            }
        }).then(async (result) => {
            if (!result.isConfirmed) return;
            const pilihan = result.value;

            $('#syncBtn').prop('disabled', true).html(loadingIcon + 'Sinkronisasi...');
            $('#syncStatusBox').show();
            const statusContent = $('#syncStatusContent').empty();

            for (let type of pilihan) {
                const typeCard = $(`
                    <div class="card mb-3 shadow-sm border-0">
                        <div class="card-header d-flex justify-content-between align-items-center" style="cursor:pointer">
                            <strong class="text-capitalize">${type}</strong>
                            <i data-feather="chevron-down" class="toggle-icon"></i>
                        </div>
                        <div class="card-body collapse show" id="body-${type}"></div>
                    </div>
                `);
                statusContent.append(typeCard);
                feather.replace();

                const bodyDiv = typeCard.find(`#body-${type}`);
                let offset = 0;
                let hasMore = true;
                const subItemStatus = {};

                while (hasMore) {
                    // Tambahkan spinner sementara
                    const loadingDiv = $(`<div class="text-center text-muted mb-2 spinner-loading">
        <span class="spinner-border spinner-border-sm me-1"></span> Mengambil data...
    </div>`);
                    bodyDiv.append(loadingDiv);

                    try {
                        const res = await $.ajax({
                            url: '<?= base_url('/panel/sinkronisasi/proses') ?>',
                            type: 'POST',
                            data: {
                                token,
                                url,
                                type,
                                offset
                            },
                            dataType: 'json'
                        });

                        // Hapus spinner saat data sudah diterima
                        loadingDiv.remove();

                        if (!res.success) throw new Error(res.message || 'Sinkronisasi gagal');

                        // Iterasi semua sub-item
                        if (res.data && res.data[type]) {
                            for (const key in res.data[type]) {
                                const item = res.data[type][key];
                                const berhasil = item.berhasil || 0;
                                const gagal = item.gagal?.length || 0;
                                const total = berhasil + gagal;
                                const percent = total > 0 ? Math.round((berhasil / total) * 100) : 0;

                                subItemStatus[key] = {
                                    berhasil,
                                    gagal
                                };

                                const progressHtml = `
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-semibold">${key}</span>
                        <span class="small text-muted">${berhasil}/${total}</span>
                    </div>
                    <div class="progress rounded-pill mb-2" style="height:12px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: ${percent}%;"></div>
                        <div class="progress-bar bg-danger" role="progressbar" style="width: ${100 - percent}%;"></div>
                    </div>
                `;

                                let subItemDiv = bodyDiv.find(`.subitem-${key}`);
                                if (!subItemDiv.length) {
                                    bodyDiv.append(`<div class="subitem-${key} mb-2">${progressHtml}</div>`);
                                } else {
                                    subItemDiv.html(progressHtml);
                                }
                            }
                        }

                        hasMore = res.has_more;
                        offset += 500;

                    } catch (err) {
                        loadingDiv.remove(); // pastikan spinner hilang jika error
                        bodyDiv.append(`<div class="text-danger">Gagal: ${err.message}</div>`);
                        hasMore = false;
                    }
                }


                // Collapsible toggle
                typeCard.find('.card-header').on('click', function() {
                    bodyDiv.collapse('toggle');
                    const icon = $(this).find('.toggle-icon');
                    icon.toggleClass('rotate-180');
                });
            }

            $('#syncBtn').prop('disabled', false).html('<i data-feather="refresh-cw"></i> Sinkronisasi Sekarang');
            feather.replace();
        });
    });
</script>
<?= $this->endSection(); ?>