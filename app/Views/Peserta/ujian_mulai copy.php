<?= $this->extend('Layout/main_peserta'); ?>
<?= $this->section('css'); ?>
<!-- Tambah CSS untuk navigasi soal -->
<style>
    .btn-check:checked+.btn,
    .btn-check:checked+.btn * {
        color: #fff !important;
    }

    .soal-item {

        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .soal-item h5 {
        font-weight: 600;
    }

    .form-check {
        margin-bottom: 10px;
    }

    .jodohkan-pair {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .jodohkan-pair label {
        width: 40%;
        font-weight: 500;
    }

    .jodohkan-kiri.active {
        border: 2px solid #0d6efd;
        background-color: #e9f3ff;
    }

    .jodohkan-pair input {
        flex: 1;
    }

    .opsi-btn {
        min-width: 200px;
        text-align: left;
        padding: 0.75rem 1rem;
        border-radius: 12px;
        font-size: 16px;
    }

    /* Hover: lebih soft dari active */
    .btn-check+.btn-outline-primary:hover {
        background-color: #f5e9ff !important;
        color: #6f42c1 !important;
        border-color: #d3bff0 !important;
    }

    /* Active (checkbox terpilih): warna ungu */
    .btn-check:checked+.btn-outline-primary,
    .btn-check:checked+.btn {
        background-color: #6f42c1 !important;
        color: white !important;
        border-color: #6f42c1 !important;
    }

    /* Transition agar smooth */
    .btn-check+.btn {
        transition: all 0.2s ease-in-out !important;
    }
</style>

<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">
            <div class="widget widget-three shadow-sm mb-3 p-2 rounded">
                <div class="widget-content d-flex justify-content-between align-items-center flex-wrap px-2 py-3">
                    <div class="d-flex align-items-center gap-3">
                        <i data-feather="book-open" class="text-primary" style="width: 28px; height: 28px;"></i>
                        <div>
                            <h5 id="ujian-title" class="fw-bold mb-1">Judul Ujian</h5>
                            <p id="ujian-durasi" class="mb-0 text-muted small">Durasi: -- menit</p>
                        </div>
                    </div>
                    <div>
                        <i data-feather="clock" class="me-1 text-danger"></i>
                        <span id="timer" class="badge bg-danger fs-6 px-3 py-2">--:--</span>
                    </div>
                </div>


            </div>
            <div class="widget widget-three shadow-sm p-4 rounded">
                <div class="widget-heading row justify-content-between align-items-center mb-4">


                </div>

                <div class="widget-content">
                    <form id="ujianForm">
                        <div id="loader" style="display:none; text-align:center; margin-top:20px;">
                            <i data-feather="loader" class="spin" style="width:32px;height:32px;"></i>
                            <p>Sedang memuat soal...</p>
                        </div>
                        <div id="soal-container" class="mb-4"></div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" id="btn-prev" disabled>Sebelumnya</button>
                            <button type="button" class="btn btn-outline-primary" id="btn-next">Selanjutnya</button>
                            <button type="button" class="btn btn-success d-none" id="btn-selesai">Selesai</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
<?= $this->section('pagejs'); ?>
<script>
    let isLoadingSoal = false;
    let soalList = [];
    let indexSoal = 0;
    let simpanTimeout;
    let durasiMenit = <?= $ujian['durasi_ujian'] ?>;
    let durasi = durasiMenit * 60;
    let cacheKey = 'jawabanCache_<?= $ujian['id'] ?>';
    let localTimeKey = 'ujian_start_time_<?= $ujian['id'] ?>';
    const statusUjian = '<?= $status_ujian ?>';
    const soalTerakhirKey = 'soal_terakhir';
    console.log(statusUjian);
    if (statusUjian === 'mulai') {
        localStorage.removeItem(cacheKey);
        localStorage.removeItem(soalTerakhirKey);
    }
    $(document).ready(function() {
        $.ajax({
            url: '<?= base_url('peserta/ujian/get-ujian/' . $ujianId) ?>',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status) {

                    const ujian = response.ujian;
                    const hasil = response.hasil;
                    const timerEl = document.getElementById('timer');
                    const waktuMulai = new Date(hasil.waktu_mulai);
                    const waktuMulaiMs = waktuMulai.getTime();
                    const durasiMenit = parseInt(ujian.durasi_ujian);
                    const waktuSelesai = waktuMulaiMs + durasiMenit * 60 * 1000;

                    const serverTime = <?= round(microtime(true) * 1000) ?>;
                    const clientTime = Date.now();
                    const offset = clientTime - serverTime;

                    const interval = setInterval(() => {
                        const now = Date.now() - offset;
                        const sisa = Math.floor((waktuSelesai - now) / 1000);
                        if (sisa <= 0) {
                            clearInterval(interval);
                            timerEl.textContent = '00:00';
                            $('#ujianForm').submit();
                            return;
                        }
                        const m = String(Math.floor(sisa / 60)).padStart(2, '0');
                        const s = String(sisa % 60).padStart(2, '0');
                        timerEl.textContent = `${m}:${s}`;
                    }, 1000);

                } else {
                    alert('Gagal memuat data ujian: ' + response.message);
                    window.location.href = '<?= base_url('peserta/home') ?>';
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Terjadi kesalahan saat mengambil data ujian.');
            }
        });
        $('#loader').show();
        // Load soal dari API
        $.getJSON("<?= base_url('peserta/ujian/soal/') . $ujian['id'] ?>", function(res) {
            if (res.status) {
                soalList = res.data;
                $('#ujian-title').text("<?= esc($ujian['nama_ujian']) ?>");
                $('#ujian-durasi').text("Durasi: <?= $ujian['durasi_ujian'] ?> menit");
                const cache = JSON.parse(localStorage.getItem(cacheKey) || '{}');
                if (Object.keys(cache).length === 0) {
                    // ambil dari server kalau localstorage kosong
                    $.get('<?= base_url('peserta/ujian/get-jawaban/' . $ujian['id']) ?>', function(res) {
                        if (res.status && res.data && Object.keys(res.data).length > 0) {
                            const newCache = {};
                            for (const [soalId, jawaban] of Object.entries(res.data)) {
                                newCache[soalId] = jawaban;
                                newCache[soalId + '_last'] = JSON.stringify(jawaban);
                            }
                            localStorage.setItem(cacheKey, JSON.stringify(newCache));
                        }
                    });
                } else {
                    loadFromLocalStorage();
                }
                const lastIndex = parseInt(localStorage.getItem('soal_terakhir'), 10);
                if (!isNaN(lastIndex) && lastIndex >= 0 && lastIndex < soalList.length) {
                    indexSoal = lastIndex;
                }
                showSoal();
                setTimeout(() => {
                    isiJawabanDariCache(soalList[0].id); // isi jawaban pertama
                }, 100); // beri delay kecil agar elemen siap
                $('#loader').hide();
            }
        });

        $('#btn-next').click(() => {
            simpanJawabanAktif();
            if (indexSoal < soalList.length - 1) {
                indexSoal++;
                showSoal();
            }
        });

        $('#btn-prev').click(() => {
            simpanJawabanAktif();
            if (indexSoal > 0) {
                indexSoal--;
                showSoal();
            }
        });


        // Event input berubah
        $(document).on('change input', '[name^="jawaban["]', function() {

            autoSimpanJawaban();
        });
    });
    // Debounce auto simpan jawaban aktif
    function autoSimpanJawaban() {
        clearTimeout(simpanTimeout);
        simpanTimeout = setTimeout(() => {
            simpanJawabanAktif();
        }, 1500);
    }

    function simpanJawabanAktif(force = false) {

        if (isLoadingSoal) {
            console.warn('Skip simpan: soal masih loading');
            return;
        }
        const soal = soalList[indexSoal];
        const soalId = soal.id;
        const jenis = soal.jenis_soal;

        const $inputs = $(`[name^="jawaban[${soalId}"]`);
        if ($inputs.length === 0) return;

        let jawaban = {};

        switch (jenis) {
            case 'pg':
                $inputs.each(function() {
                    if ($(this).is(':checked')) {
                        jawaban = {
                            value: $(this).val()
                        };
                    }
                });
                break;

            case 'mpg':
                const selected = [];
                $inputs.each(function() {
                    if ($(this).is(':checked')) {
                        selected.push($(this).val());
                    }
                });
                jawaban = {
                    values: selected
                };
                break;

            case 'benar_salah':
                $inputs.each(function() {
                    if ($(this).is(':checked')) {
                        const label = $(this).data('label');
                        if (label) {
                            jawaban[label] = $(this).val();
                        }
                    }
                });
                break;

            case 'esai':
            case 'isian':
                jawaban = {
                    value: $inputs.first().val() || ''
                };
                break;

            case 'jodohkan':
                jawaban = pasangan[soalId] || {};
                break;

            default:
                jawaban = {};
        }

        const dataPost = {
            ujian_id: <?= json_encode($ujian['id']) ?>,
            jawaban: {
                [soalId]: jawaban
            }
        };

        let cache = JSON.parse(localStorage.getItem(cacheKey) || '{}');
        const jawabanString = JSON.stringify(jawaban);

        if (force || jawabanString !== cache[soalId + '_last']) {
            cache[soalId] = jawaban;
            cache[soalId + '_last'] = jawabanString;
            localStorage.setItem(cacheKey, JSON.stringify(cache));

            $.ajax({
                url: '<?= base_url('peserta/ujian/simpan-jawaban') ?>',
                method: 'POST',
                data: dataPost,
                dataType: 'json',
                success: function(res) {
                    if (!res.status) {
                        console.warn('Gagal simpan jawaban:', res.message || 'Unknown');
                    }
                },
                error: function(err) {
                    console.warn('Error saat simpan jawaban:', err);
                }
            });
        }
    }

    function loadFromLocalStorage() {
        const cache = JSON.parse(localStorage.getItem(cacheKey) || '{}');
        for (const [soalId, _] of Object.entries(cache)) {
            if (!soalId.includes('_last')) {
                isiJawabanDariCache(soalId);
            }
        }
    }


    function isiJawabanDariCache(soalId) {
        const cache = JSON.parse(localStorage.getItem(cacheKey) || '{}');
        const serialized = cache[soalId];
        if (!serialized || typeof serialized !== 'object') return;

        const soal = soalList.find(s => s.id === soalId);
        if (!soal) return;

        const jenis = soal.jenis_soal;

        if (jenis === 'pg') {
            const val = serialized.value;
            if (val) {
                $(`[name="jawaban[${soalId}]"][value="${val}"]`)
                    .prop('checked', true)
                    .trigger('change');
            }
        } else if (jenis === 'mpg') {
            const values = serialized.values || [];
            values.forEach(val => {
                $(`[name="jawaban[${soalId}][]"][value="${val}"]`)
                    .prop('checked', true)
                    .trigger('change');
            });
        } else if (jenis === 'benar_salah') {
            let parsed = serialized;
            if (typeof parsed === 'string') {
                try {
                    parsed = JSON.parse(parsed);
                } catch (e) {
                    console.warn('Gagal parsing benar_salah untuk soal', soalId, serialized);
                    return;
                }
            }

            Object.entries(parsed).forEach(([label, val]) => {
                if (label && val) {
                    $(`[name="jawaban[${soalId}][${label}]"][value="${val}"]`)
                        .prop('checked', true)
                        .trigger('change');
                }
            });
        } else if (jenis === 'esai' || jenis === 'isian') {
            const val = serialized.value || '';
            $(`[name="jawaban[${soalId}]"]`).val(val).trigger('change');
        } else if (jenis === 'jodohkan') {
            pasangan[soalId] = serialized;
            pasangan_teks[soalId] = {};

            soal.opsi.forEach(op => {
                const kiri = op.label;
                const kanan = serialized[kiri];
                if (kanan) {
                    pasangan_teks[soalId][op.teks] = kanan;
                    $(`#jodohkan-kiri-${soalId} .jodohkan-kiri[data-id="${kiri}"]`).remove();
                    $(`#jodohkan-kanan-${soalId} .jodohkan-kanan[data-id="${kanan}"]`).remove();
                }
            });

            renderPasangan(soalId);
        }
        isLoadingSoal = false;
    }

    function showSoal() {
        isLoadingSoal = true;
        const item = soalList[indexSoal];
        let opsiHtml = '';
        const storedJawaban = JSON.parse(localStorage.getItem('jawaban_ujian') || '{}');
        // const jawabanSementara = storedJawaban[item.id];

        switch (item.jenis_soal) {
            case 'pg':
                opsiHtml = item.opsi.map(op => `
                <input type="radio" class="btn-check" name="jawaban[${item.id}]" value="${op.label}" id="opsi-${op.id}" autocomplete="off">
                <label class="btn btn-outline-primary w-100 text-start mb-2" for="opsi-${op.id}">${op.teks}</label>
            `).join('');
                break;

            case 'mpg':
                opsiHtml = item.opsi.map(op => `
                <input type="checkbox" class="btn-check mpg-opsi" name="jawaban[${item.id}][]" value="${op.label}" id="opsi-${op.id}" autocomplete="off">
                <label class="btn btn-outline-primary w-100 text-start mb-2" for="opsi-${op.id}">${op.teks}</label>
            `).join('');
                break;
            case 'benar_salah':
                opsiHtml = item.opsi.map((op, i) => `
        <div class="border rounded p-3 mb-2">
            <p class="mb-2">${op.teks}</p>
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" 
                           name="jawaban[${item.id}][${op.label}]" 
                           value="Benar" 
                           id="benar-${item.id}-${op.label}" 
                           data-label="${op.label}">
                    <label class="form-check-label" for="benar-${item.id}-${op.label}">Benar</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" 
                           name="jawaban[${item.id}][${op.label}]" 
                           value="Salah" 
                           id="salah-${item.id}-${op.label}" 
                           data-label="${op.label}">
                    <label class="form-check-label" for="salah-${item.id}-${op.label}">Salah</label>
                </div>
            </div>
        </div>
    `).join('');
                break;
            case 'jodohkan':
                const kiri = item.opsi.map(op => `
        <button type="button" class="btn btn-outline-primary m-1 jodohkan-kiri"
            data-id="${op.label}" data-teks="${op.teks}" data-soal="${item.id}">
            ${op.teks}
        </button>`).join('');

                const kanan = item.opsi.map(op => `
        <button type="button" class="btn btn-outline-secondary m-1 jodohkan-kanan"
            data-id="${op.pasangan}" data-teks="${op.pasangan}" data-soal="${item.id}">
            ${op.pasangan}
        </button>`).join('');

                opsiHtml = `
        <div class="row">
            <div class="col-md-6 text-start">
                <h6>Pilihan Kiri:</h6>
                <div id="jodohkan-kiri-${item.id}">${kiri}</div>
            </div>
            <div class="col-md-6 text-end">
                <h6>Pilihan Kanan:</h6>
                <div id="jodohkan-kanan-${item.id}">${kanan}</div>
            </div>
        </div>
        <input type="hidden" name="jawaban[${item.id}]" id="jawaban-${item.id}" />
        <div id="hasil-pasangan-${item.id}" class="mt-4"></div>
    `;
                break;


            case 'isian':
                opsiHtml = `
                    <div class="mb-3">
                        <input type="text" class="form-control" name="jawaban[${item.id}]" placeholder="Tulis jawaban singkat di sini..." />
                    </div>
                `;
                break;

            case 'esai':
                opsiHtml = `
                <div class="mb-3">
                    <textarea class="form-control" name="jawaban[${item.id}]" rows="4" placeholder="Tulis jawabanmu di sini..."></textarea>
                </div>
            `;
                break;

            default:
                opsiHtml = '<p class="text-muted">Jenis soal tidak dikenali.</p>';
        }
        const badgeLabel = {
            'pg': '<span class="badge bg-primary">Pilihan Ganda</span>',
            'mpg': '<span class="badge bg-warning text-dark">Pilihan Ganda Majemuk</span>',
            'benar_salah': '<span class="badge bg-info text-dark">Benar / Salah</span>',
            'jodohkan': '<span class="badge bg-success">Menjodohkan</span>',
            'isian': '<span class="badge bg-secondary">Isian Singkat</span>',
            'esai': '<span class="badge bg-secondary">Soal Esai</span>',
        };

        const html = `
           
                <div class="position-absolute top-0 end-0 m-3">
                    ${badgeLabel[item.jenis_soal] || ''}
                </div>
                <h5 class="mb-3">Soal ${indexSoal + 1}:</h5>
               <p class="mb-4 fs-5 lh-base fw-semibold">${item.pertanyaan}</p>
                ${opsiHtml}
            
            `;

        // Disable tombol dulu biar gak klik sebelum soal siap
        $('#btn-next, #btn-prev').prop('disabled', true);

        $('#soal-container').html(html);
        setTimeout(() => {
            isiJawabanDariCache(item.id);

            // Setelah isi jawaban dari cache selesai, enable tombol kembali
            $('#btn-prev').prop('disabled', indexSoal === 0);
            $('#btn-next').toggle(indexSoal < soalList.length - 1);
            $('#btn-selesai').toggleClass('d-none', indexSoal !== soalList.length - 1);
            $('#btn-next, #btn-prev').prop('disabled', false);
            isLoadingSoal = false;
            localStorage.setItem('soal_terakhir', indexSoal.toString());
        }, 100);
    }


    let selectedKiri = null;
    let pasangan = {};
    let pasangan_teks = {};

    $(document).on('click', '.jodohkan-kiri', function() {
        selectedKiri = $(this).data('id');
        selectedKiriText = $(this).data('teks');
        $('.jodohkan-kiri').removeClass('active');
        $(this).addClass('active');
    });

    $(document).on('click', '.jodohkan-kanan', function() {
        if (!selectedKiri) return;

        const kanan = $(this).data('id');
        const kananText = $(this).data('teks');
        const soalId = $(this).data('soal');

        // Simpan pasangan
        pasangan[soalId] = pasangan[soalId] || {};
        pasangan_teks[soalId] = pasangan_teks[soalId] || {};
        pasangan[soalId][selectedKiri] = kanan;
        pasangan_teks[soalId][selectedKiriText] = kananText;

        // Hilangkan tombol yang sudah dipilih
        $(`#jodohkan-kiri-${soalId} .jodohkan-kiri[data-id="${selectedKiri}"]`).remove();
        $(`#jodohkan-kanan-${soalId} .jodohkan-kanan[data-id="${kanan}"]`).remove();

        // Render hasil pasangan
        renderPasangan(soalId);

        $('.jodohkan-kiri').removeClass('active');
        selectedKiri = null;
    });

    function renderPasangan(soalId) {
        const data = pasangan_teks[soalId];
        let html = `<strong>Pasangan:</strong><ul class="list-group mt-2">`;

        for (const [kiri, kanan] of Object.entries(data)) {
            html += `
            <li class="list-group-item d-flex justify-content-between align-items-center">
                ${kiri} ➜ ${kanan}
                <button type="button" class="btn btn-sm btn-danger hapus-pasangan" 
                    data-soal="${soalId}" data-kiri="${kiri}">Hapus</button>
            </li>
        `;
        }

        html += `</ul>`;
        $(`#hasil-pasangan-${soalId}`).html(html);
        $(`#jawaban-${soalId}`).val(JSON.stringify(pasangan[soalId]));
    }

    $(document).on('click', '.hapus-pasangan', function() {
        const soalId = $(this).data('soal');
        const kiriText = $(this).data('kiri');

        const kananText = pasangan_teks[soalId][kiriText];
        const kiriLabel = Object.keys(pasangan[soalId]).find(key =>
            itemByLabel(soalId, key) === kiriText
        );
        const kananLabel = pasangan[soalId][kiriLabel];

        // Kembalikan tombol
        $(`#jodohkan-kiri-${soalId}`).append(`
        <button type="button" class="btn btn-outline-primary m-1 jodohkan-kiri"
            data-id="${kiriLabel}" data-teks="${kiriText}" data-soal="${soalId}">
            ${kiriText}
        </button>`);

        $(`#jodohkan-kanan-${soalId}`).append(`
        <button type="button" class="btn btn-outline-secondary m-1 jodohkan-kanan"
            data-id="${kananLabel}" data-teks="${kananText}" data-soal="${soalId}">
            ${kananText}
        </button>`);

        // Hapus pasangan
        delete pasangan[soalId][kiriLabel];
        delete pasangan_teks[soalId][kiriText];

        renderPasangan(soalId);
    });

    // Fungsi bantu ambil teks dari label
    function itemByLabel(soalId, label) {
        const soal = soalList.find(s => s.id === soalId);
        const op = soal?.opsi?.find(o => o.label === label);
        return op?.teks ?? label;
    }




    $(document).on('keydown', function(e) {
        if (isLoadingSoal) return;

        if (e.key === 'ArrowRight') {
            simpanJawabanAktif();
            if (indexSoal < soalList.length - 1) {
                indexSoal++;
                showSoal();
            }
        } else if (e.key === 'ArrowLeft') {
            simpanJawabanAktif();
            if (indexSoal > 0) {
                indexSoal--;
                showSoal();
            }
        }
    });
</script>
<script>
    $(document).ready(function() {
        $('#ujianForm').on('submit', function(e) {
            e.preventDefault();

            simpanJawabanAktif(true); // kirim jawaban terakhir

            const ujianId = '<?= $ujian['id'] ?>';
            const cacheKey = 'jawabanCache_' + ujianId;

            $.ajax({
                url: '<?= base_url('peserta/ujian/selesai/' . $ujian['id']) ?>',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.status) {
                        localStorage.removeItem(cacheKey);
                        localStorage.removeItem('soal_terakhir');
                        window.location.href = res.redirect || '<?= base_url('peserta') ?>';
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal menyimpan',
                            text: res.message || 'Terjadi kesalahan saat menyimpan jawaban.'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Server tidak merespons. Silakan coba lagi.'
                    });
                }
            });
        });
        $('#btn-selesai').on('click', function() {
            Swal.fire({
                title: 'Yakin ingin menyelesaikan ujian?',
                text: 'Jawaban tidak bisa diubah setelah ini!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Selesai!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim AJAX POST ke controller selesai
                    $.ajax({
                        url: '<?= base_url('peserta/ujian/selesai/' . $ujian['id']) ?>',
                        method: 'POST',
                        dataType: 'json',
                        success: function(res) {
                            if (res.status) {
                                Swal.fire({
                                    title: 'Ujian Diselesaikan',
                                    html: `
                                            <p><strong>${res.message}</strong></p>
                                            <hr>
                                            <p><strong>Jumlah Benar:</strong> ${res.jumlah_benar}</p>
                                            <p><strong>Jumlah Salah:</strong> ${res.jumlah_soal - res.jumlah_benar}</p>
                                            <p><strong>Nilai:</strong> ${res.nilai}</p>
                                        `,
                                    icon: 'success',
                                    timer: 4000,
                                    showConfirmButton: false
                                }).then(() => {
                                    localStorage.removeItem(cacheKey);
                                    localStorage.removeItem('soal_terakhir');
                                    window.location.href = '<?= base_url('peserta/home') ?>';
                                });
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        },

                        error: function(xhr, status, error) {
                            Swal.fire('Error', 'Terjadi kesalahan saat menyelesaikan ujian.', 'error');
                            console.error(error);
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection(); ?>