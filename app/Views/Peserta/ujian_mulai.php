<?= $this->extend('Layout/main_peserta'); ?>
<?= $this->section('css'); ?>

<!-- Tambah CSS untuk navigasi soal -->
<style>
    .btn-check:checked+.btn,
    .btn-check:checked+.btn * {
        color: #fff !important;
    }

    #soal-container img {
        cursor: zoom-in;
        transition: 0.3s;
        max-width: 100%;
        height: auto;
    }

    /* Backdrop transparan dengan blur */
    .modal-backdrop.show {
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(4px);
    }

    /* Animasi zoom */
    @keyframes zoomIn {
        from {
            transform: scale(0.8);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .animate-zoom {
        animation: zoomIn 0.3s ease-out;
    }

    /* Hover efek */
    #previewImage:hover {
        transform: scale(1.02);
        transition: transform 0.3s ease;
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
<style>
    body.dark .mpg-option {
        display: flex !important;
    }

    /* Wrapper MPG */
    .mpg-option {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 2px;
        border-bottom: 1px solid #ececec;
        cursor: pointer;
        transition: 0.2s;
    }

    /* Hover effect */
    .mpg-option:hover {
        background: #f9f9f9;
    }

    /* Hide default checkbox */
    .mpg-checkbox {
        appearance: none;
        -webkit-appearance: none;
        width: 18px;
        height: 18px;
        border: 2px solid #999;
        border-radius: 4px;
        position: relative;
        cursor: pointer;
        transition: 0.25s;
    }

    /* Animasi centang */
    .mpg-checkbox:checked {
        border-color: #0d6efd;
        background: #0d6efd;
    }

    .mpg-checkbox:checked::after {
        content: "";
        position: absolute;
        top: 2px;
        left: 5px;
        width: 5px;
        height: 10px;
        border-right: 2px solid white;
        border-bottom: 2px solid white;
        transform: rotate(45deg) scale(1);
        opacity: 1;
        transition: 0.2s ease-out;
    }

    /* Animasi muncul */
    .mpg-checkbox::after {
        opacity: 0;
        transform: scale(0.5);
    }

    /* Teks MPG */
    .mpg-text {
        font-size: 15px;
        line-height: 20px;
    }

    /* Hint kecil */
    .mpg-hint {
        font-size: 12px;
        color: #1976d2;
        margin-bottom: 4px;
        font-style: italic;
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

                        <div id="navigasiSoal" class="d-flex justify-content-between align-items-center mt-4">
                            <button type="button" class="btn btn-outline-secondary" id="btn-prev" disabled><i data-feather="arrow-left-circle"></i></button>
                            <button type="button" id="btnDaftarSoal" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalDaftarSoal">
                                <i data-feather="list"></i> Soal
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="btn-next"><i data-feather="arrow-right-circle"></i></button>
                            <button type="button" class="btn btn-success d-none" id="btn-selesai">Selesai</button>

                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Daftar Soal -->
<div class="modal fade" id="modalDaftarSoal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-white">
            <div class="modal-header">
                <h5 class="modal-title">Daftar Soal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="gridDaftarSoal" class="d-flex flex-wrap gap-2"></div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Preview Gambar -->
<!-- Modal Preview Gambar Modern -->
<div class="modal fade" id="imgPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen m-0">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="modal-body d-flex justify-content-center align-items-center p-0 position-relative">

                <!-- Tombol close -->
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-4 fs-3" data-bs-dismiss="modal" aria-label="Close"></button>

                <!-- Gambar -->
                <img id="previewImage" src=""
                    class="img-fluid rounded shadow-lg animate-zoom"
                    style="max-height: 90vh; cursor: grab;" />
            </div>
        </div>
    </div>
</div>



<?= $this->endSection(); ?>
<?= $this->section('pagejs'); ?>
<script>

</script>
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
    const soalStorageKey = 'soalList_<?= $ujian['id'] ?>';
    const storedSoal = localStorage.getItem(soalStorageKey);


    $(document).ready(function() {
        document.querySelectorAll('.ql-formula').forEach(el => {
            // ambil data-value asli
            const val = el.getAttribute('data-value');
            // buang isi render lama
            el.innerHTML = '';
            // render ulang dengan KaTeX
            katex.render(val, el);
        });


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

        if (statusUjian === 'mulai') {
            // Reset localStorage
            localStorage.removeItem(cacheKey);
            localStorage.removeItem(soalTerakhirKey);
            localStorage.removeItem(soalStorageKey);
            localStorage.removeItem(localTimeKey);

            // Fetch soal baru dari server
            $.getJSON("<?= base_url('peserta/ujian/soal/') . $ujian['id'] ?>", function(res) {
                if (res.status) {
                    soalList = res.data;
                    localStorage.setItem(soalStorageKey, JSON.stringify(soalList));
                    localStorage.setItem(localTimeKey, Date.now());
                    afterSoalLoaded();
                } else {
                    alert('Gagal memuat soal: ' + res.message);
                }
            });

        } else {
            const storedSoal = localStorage.getItem(soalStorageKey);
            if (storedSoal) {
                soalList = JSON.parse(storedSoal);
                afterSoalLoaded();
            } else {
                // Cadangan jika datanya belum ada (misal user clear cache saat status "lanjutkan")
                $.getJSON("<?= base_url('peserta/ujian/soal/') . $ujian['id'] ?>", function(res) {
                    if (res.status) {
                        soalList = res.data;
                        localStorage.setItem(soalStorageKey, JSON.stringify(soalList));
                        afterSoalLoaded();
                    } else {
                        alert('Gagal memuat soal: ' + res.message);
                    }
                });
            }
        }


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

    function cekSemuaSoalTerjawab() {
        const cache = JSON.parse(localStorage.getItem(cacheKey) || '{}');
        return soalList.every((soal) => {
            const key = soal.id + '_last';
            return cache[key] !== undefined && cache[key] !== null && cache[key] !== '';
        });
    }

    function renderDaftarSoal() {
        const container = document.getElementById('gridDaftarSoal');
        container.innerHTML = '';

        const cache = JSON.parse(localStorage.getItem(cacheKey) || '{}');

        soalList.forEach((soal, i) => {
            const soalId = soal.id;
            const jawaban = cache[soalId];

            let isAnswered = false;

            if (jawaban !== undefined && jawaban !== null) {
                if (Array.isArray(jawaban)) {
                    // MPG (checkbox) → dianggap menjawab kalau ada isinya
                    isAnswered = jawaban.length > 0;
                } else if (typeof jawaban === 'object') {
                    if ('values' in jawaban) {
                        // Menjodohkan
                        isAnswered = Array.isArray(jawaban.values) && jawaban.values.length > 0;
                    } else if ('value' in jawaban) {
                        // Isian Singkat
                        isAnswered = String(jawaban.value).trim() !== '';
                    } else {
                        // PG atau lainnya dalam bentuk object
                        isAnswered = Object.keys(jawaban).length > 0;
                    }
                } else {
                    // Jawaban string (PG, Benar/Salah, Esai singkat)
                    isAnswered = String(jawaban).trim() !== '';
                }
            }

            const btn = document.createElement('button');
            btn.className = 'btn ' + (isAnswered ? 'btn-success' : 'btn-outline-secondary');
            btn.textContent = i + 1;
            btn.style.width = '50px';
            btn.style.height = '50px';
            btn.style.fontSize = '12px';
            btn.style.margin = '4px';
            btn.style.borderRadius = '50px';
            btn.style.padding = '2px';

            btn.addEventListener('click', () => {
                indexSoal = i;
                showSoal();
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalDaftarSoal'));
                modal.hide();
            });

            container.appendChild(btn);
        });
    }



    function afterSoalLoaded() {
        $('#ujian-title').text("<?= esc($ujian['nama_ujian']) ?>");
        $('#ujian-durasi').text("Durasi: <?= $ujian['durasi_ujian'] ?> menit");

        const cache = JSON.parse(localStorage.getItem(cacheKey) || '{}');
        if (Object.keys(cache).length === 0) {
            // ambil dari server kalau cache kosong
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
            isiJawabanDariCache(soalList[indexSoal].id);
        }, 100);
        $('#loader').hide();
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
                const maxBenar = item.max_select ?? item.opsi.length;

                opsiHtml = `
                        <div>
                            <div class="mpg-hint">Boleh memilih lebih dari satu</div>

                            ${item.opsi.map(op => `
                                <label class="mpg-option" for="opsi-${op.id}">
                                    <input type="checkbox" 
                                        class="mpg-checkbox" 
                                        id="opsi-${op.id}"
                                        name="jawaban[${item.id}][]"
                                        value="${op.label}">
                                    <div class="mpg-text">${op.teks}</div>
                                </label>
                            `).join('')}
                        </div>
                    `;
                // 🔹 Tambahkan skrip pembatas pilihan setelah render
                setTimeout(() => {
                    const $checkboxes = $(`[name="jawaban[${item.id}][]"]`);

                    // 🔥 Fungsi pembatas pilihan (dipakai saat klik & saat isi cache)
                    const enforceMaxSelect = () => {
                        const checked = $checkboxes.filter(':checked');

                        if (checked.length > maxBenar) {
                            // Uncheck yang terakhir dipilih (DOM paling akhir)
                            const last = checked.last();
                            last.prop('checked', false);

                            Swal.fire({
                                icon: 'warning',
                                title: 'Batas pilihan tercapai',
                                text: `Kamu hanya boleh memilih maksimal ${maxBenar} jawaban.`,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    };

                    // 🔹 Event: ketika siswa mencentang
                    $checkboxes.on('change', enforceMaxSelect);

                    // 🔹 Trigger otomatis saat cache mengisi jawaban
                    //    → ini memperbaiki masalah "jawaban muncul 3 lagi"
                    enforceMaxSelect();

                }, 200);
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
                // 👉 Duplikat dan acak array opsi kiri dan kanan
                const opsiKiri = [...item.opsi];
                const opsiKanan = [...item.opsi];

                // Fungsi acak
                function shuffleArray(arr) {
                    for (let i = arr.length - 1; i > 0; i--) {
                        const j = Math.floor(Math.random() * (i + 1));
                        [arr[i], arr[j]] = [arr[j], arr[i]];
                    }
                }

                // Acak isi
                shuffleArray(opsiKiri);
                shuffleArray(opsiKanan);

                // Render tombol kiri (teks)
                const kiri = opsiKiri.map(op => `
                <button type="button" class="btn btn-outline-primary m-1 jodohkan-kiri"
                    data-id="${op.label}" data-teks="${op.teks}" data-soal="${item.id}">
                    ${op.teks}
                </button>`).join('');

                // Render tombol kanan (pasangan)
                const kanan = opsiKanan.map(op => `
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


        renderDaftarSoal();

        // Cegah submit saat tekan Enter di soal isian atau esai
        document.querySelectorAll('input[name^="jawaban["], textarea[name^="jawaban["]').forEach((el) => {
            el.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    // Cegah Enter agar tidak memicu submit form
                    e.preventDefault();
                }
            });
        });
    }

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
            renderDaftarSoal(); // ⬅️ Tambahkan ini di sini

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
                const cb = $(`[name="jawaban[${soalId}][]"][value="${val}"]`);

                // Hanya centang jika checkbox ada di UI
                if (cb.length) {
                    cb.prop('checked', true);
                }
            });

            // 🔥 Setelah centang berdasarkan cache,
            // panggil event pembatas pilihan agar UI tidak melebihi max select
            const $checkboxes = $(`[name="jawaban[${soalId}][]"]`);
            const maxBenar = soal.max_select ?? soal.opsi.length;

            const checked = $checkboxes.filter(':checked');
            if (checked.length > maxBenar) {
                // Uncheck kelebihan (yang terakhir)
                checked.slice(maxBenar).prop('checked', false);
            }

            // Trigger change sekali agar sinkron (optional, tidak wajib)
            $checkboxes.first().trigger('change');
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
                        localStorage.removeItem(soalTerakhirKey);
                        localStorage.removeItem(soalStorageKey); // ← ini yang kurang
                        localStorage.removeItem(localTimeKey); // jangan lupa ini juga

                        window.location.href = res.redirect || '<?= base_url('peserta/home') ?>';
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
            let localTimeKey = 'ujian_start_time_<?= $ujian['id'] ?>';
            simpanJawabanAktif(); // pastikan jawaban terakhir tersimpan
            const MINIMAL_MENIT = <?= $ujian['minimal_durasi'] ?? 0 ?>; // ⏱ ambil dari database (menit)
            const startTime = localStorage.getItem(localTimeKey);
            console.log(startTime);
            // 🔹 Cek waktu mulai ujian
            if (startTime) {
                const elapsedMs = Date.now() - parseInt(startTime);
                const elapsedMinutes = Math.floor(elapsedMs / 60000); // konversi ke menit

                if (elapsedMinutes < MINIMAL_MENIT) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Belum Bisa Menyelesaikan Ujian',
                        text: `Anda baru mengerjakan selama ${elapsedMinutes} menit. Minimal ${MINIMAL_MENIT} menit sebelum bisa menyelesaikan ujian.`,
                        confirmButtonText: 'Oke'
                    });
                    return; // ⛔ hentikan proses
                }
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Waktu Mulai Tidak Ditemukan',
                    text: 'Silakan muat ulang halaman ujian.',
                    confirmButtonText: 'Oke'
                });
                return;
            }

            // 🔹 Cek apakah semua soal sudah dijawab
            if (!cekSemuaSoalTerjawab()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Masih Ada Soal Belum Terjawab',
                    text: 'Silakan lanjutkan ujian dan lengkapi semua jawaban.',
                    confirmButtonText: 'Oke'
                });
                return;
            }

            // 🔹 Konfirmasi penyelesaian ujian
            Swal.fire({
                title: 'Yakin ingin menyelesaikan ujian?',
                text: 'Jawaban tidak bisa diubah setelah ini!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Selesai!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('peserta/ujian/selesai/' . $ujian['id']) ?>',
                        method: 'POST',
                        dataType: 'json',
                        success: function(res) {
                            if (res.status) {
                                let htmlContent = '';

                                if (res.tampil_nilai == 1) {
                                    htmlContent = `
                                <div style="text-align: left; font-size: 16px; line-height: 1.6;">
                                    <p><strong>Hasil:</strong> ${res.message}</p>
                                    <p><strong>Soal Benar:</strong> <span style="color:green;">${res.soal_benar}</span></p>
                                    <p><strong>Soal Salah:</strong> <span style="color:red;">${res.soal_salah}</span></p>
                                    <p><strong>Poin Benar:</strong> ${res.poin_benar} / ${res.poin_maksimal}</p>
                                    <hr style="margin: 10px 0;">
                                    <p style="font-size: 18px;">
                                        <strong>Nilai Akhir:</strong>
                                        <span style="font-size: 22px; color: blue;">${res.nilai}</span>
                                    </p>
                                </div>
                            `;
                                } else {
                                    htmlContent = `<p style="font-size:16px;">${res.message}</p>`;
                                }

                                Swal.fire({
                                    title: '✅ Ujian Telah Diselesaikan!',
                                    html: htmlContent,
                                    icon: 'success',
                                    timer: 6000,
                                    showConfirmButton: false,
                                    timerProgressBar: true,
                                    didClose: () => {
                                        localStorage.removeItem(cacheKey);
                                        localStorage.removeItem(soalTerakhirKey);
                                        localStorage.removeItem(soalStorageKey);
                                        localStorage.removeItem(localTimeKey);
                                        window.location.href = res.redirect || '<?= base_url('peserta/home') ?>';
                                    }
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

<!-- script untuk pengamanan -->
<script>
    // blok klik kanan
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });

    document.addEventListener('keydown', function(e) {
        // Blokir F12, Ctrl+Shift+I, Ctrl+U, Ctrl+S
        if (
            e.key === 'F12' ||
            (e.ctrlKey && e.shiftKey && e.key === 'I') ||
            (e.ctrlKey && e.key === 'U') ||
            (e.ctrlKey && e.key === 'S')
        ) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Akses Diblokir',
                text: 'Kombinasi keyboard ini tidak diperbolehkan saat ujian.'
            });
        }
    });

    let tabKeluarCount = 0;
    let isSwalActive = false; // 🔒 Untuk mencegah SweetAlert dobel

    window.addEventListener('blur', () => {
        if (isSwalActive) return;

        tabKeluarCount++;
        isSwalActive = true;

        Swal.fire({
            icon: 'warning',
            title: 'Peringatan!',
            text: 'Kamu meninggalkan halaman ujian!',
            timer: 2000,
            showConfirmButton: false,
            didClose: () => {
                isSwalActive = false;
            }
        });

        if (tabKeluarCount >= 5) {
            Swal.fire({
                icon: 'error',
                title: 'Ujian Diblokir!',
                text: 'Kamu terlalu sering keluar dari halaman!',
            }).then(() => {
                window.location.href = '<?= base_url('peserta/home') ?>';
            });
        }
    });

    // Klik gambar di soal → tampilkan modal modern
    $(document).on('click', '#soal-container img, #soal-container label img', function() {
        const src = $(this).attr('src');
        $('#previewImage').attr('src', src);
        $('#imgPreviewModal').modal('show');
    });

    // Scroll zoom di gambar
    let zoom = 1;
    $(document).on('wheel', '#previewImage', function(e) {
        e.preventDefault();
        if (e.originalEvent.deltaY < 0) {
            zoom += 0.1; // zoom in
        } else {
            zoom = Math.max(1, zoom - 0.1); // zoom out
        }
        $(this).css('transform', `scale(${zoom})`);
    });

    // Reset zoom saat modal ditutup
    $('#imgPreviewModal').on('hidden.bs.modal', function() {
        zoom = 1;
        $('#previewImage').css('transform', 'scale(1)');
    });


    // Deteksi printscreen
    document.addEventListener('keyup', function(e) {
        if (e.key === 'PrintScreen') {
            Swal.fire({
                icon: 'warning',
                title: 'Tangkapan layar terdeteksi!',
                text: 'Perilaku ini dicatat dan bisa menyebabkan diskualifikasi.'
            });
        }
    });
</script>
<script>
    function masukFullscreen() {
        const elem = document.documentElement;
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        }
    }

    // Jalankan saat awal masuk halaman
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'info',
            title: 'Mulai Ujian',
            text: 'Klik OK untuk memulai ujian dalam mode layar penuh.',
            confirmButtonText: 'OK',
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: true
        }).then(() => {
            masukFullscreen();

        });
    });

    // FULLSCREEN: Tambahkan juga flag ini agar tidak bentrok
    document.addEventListener('fullscreenchange', function() {
        if (!document.fullscreenElement && !isSwalActive) {
            isSwalActive = true;

            Swal.fire({
                icon: 'warning',
                title: 'Keluar dari Layar Penuh!',
                text: 'Silakan masuk kembali ke layar penuh untuk melanjutkan ujian.',
                confirmButtonText: 'Kembali ke Fullscreen',
                allowOutsideClick: false,
                allowEscapeKey: false,
            }).then(() => {
                masukFullscreen();
                isSwalActive = false;
            });
        }
    });
</script>
<?php if ($ujian['pakai_webcam'] == '1') { ?>
    <style>
        #webcamWrapper {
            position: fixed;
            top: 200px;
            right: 10px;
            width: 120px;
            height: 90px;
            z-index: 9999;
            cursor: grab;
            touch-action: none;
            /* penting untuk mobile drag */
            will-change: transform;
            /* meningkatkan performa animasi */
        }

        #webcam {
            width: 100%;
            height: 100%;
            border: 2px solid #333;
            border-radius: 8px;
            pointer-events: auto;
            background: #6f42c1;
        }
    </style>

    <div id="webcamWrapper">
        <video id="webcam" autoplay muted playsinline></video>
    </div>

    <script>
        const wrapper = document.getElementById('webcamWrapper');
        const video = document.getElementById('webcam');

        // Akses webcam
        navigator.mediaDevices.getUserMedia({
                video: true,
                audio: false
            })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(err => {
                console.error(err);
                alert('Gagal akses webcam');
            });

        let startX = 0,
            startY = 0;
        let currentX = 0,
            currentY = 0;
        let isDragging = false;

        // --- Desktop Drag ---
        wrapper.addEventListener('mousedown', e => {
            isDragging = true;
            startX = e.clientX - currentX;
            startY = e.clientY - currentY;
            wrapper.style.cursor = 'grabbing';
        });

        document.addEventListener('mousemove', e => {
            if (!isDragging) return;
            currentX = e.clientX - startX;
            currentY = e.clientY - startY;
            // Batas layar
            currentX = Math.max(0, Math.min(currentX, window.innerWidth - wrapper.offsetWidth));
            currentY = Math.max(0, Math.min(currentY, window.innerHeight - wrapper.offsetHeight));
            wrapper.style.transform = `translate(${currentX}px, ${currentY}px)`;
        });

        document.addEventListener('mouseup', () => {
            isDragging = false;
            wrapper.style.cursor = 'grab';
        });

        // --- Mobile Drag ---
        wrapper.addEventListener('touchstart', e => {
            const t = e.touches[0];
            isDragging = true;
            startX = t.pageX - currentX;
            startY = t.pageY - currentY;
        });

        wrapper.addEventListener('touchmove', e => {
            if (!isDragging) return;
            const t = e.touches[0];
            currentX = t.pageX - startX;
            currentY = t.pageY - startY;
            // Batas layar
            currentX = Math.max(0, Math.min(currentX, window.innerWidth - wrapper.offsetWidth));
            currentY = Math.max(0, Math.min(currentY, window.innerHeight - wrapper.offsetHeight));
            wrapper.style.transform = `translate(${currentX}px, ${currentY}px)`;
            e.preventDefault(); // penting supaya scroll halaman tidak ikut
        });

        wrapper.addEventListener('touchend', () => isDragging = false);
    </script>


<?php } ?>
<?= $this->endSection(); ?>