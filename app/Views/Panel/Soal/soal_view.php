<?= $this->extend('Layout/main'); ?>
<?= $this->section('content'); ?>
<style>
    .opsi-html p {
        margin: 0;
        display: inline;
    }

    .opsi-html {
        display: inline-block;
    }

    .soal-form-transition {
        display: none;
        opacity: 0;
        transform: translateY(-10px);
        transition: all 0.3s ease;
    }

    .soal-form-transition.show {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }

    form.form-aktif {
        border: 2px solid #007bff;
        box-shadow: 0 0 10px rgba(0, 123, 255, 0.2);
        transition: all 0.3s ease;
    }


    /* Custom Quill Snow Theme */
    .ql-toolbar.ql-snow {
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 0.5rem 0.5rem 0 0;
        padding: 8px;
        font-family: 'Inter', sans-serif;
    }

    .ql-container.ql-snow {
        border: 1px solid #ddd;
        border-top: none;
        border-radius: 0 0 0.5rem 0.5rem;
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        padding: 12px;
        background-color: #fff;
    }

    .ql-editor img {
        pointer-events: all;
        max-width: 100%;
        height: auto;
    }


    .ql-editor.ql-blank::before {
        color: #999;
        font-style: italic;
    }

    .ql-snow .ql-picker {
        color: #333;
    }

    .ql-snow .ql-stroke {
        stroke: #333;
    }

    .ql-snow .ql-fill {
        fill: #333;
    }

    /* Styling formula di dalam Quill */
    .ql-editor .ql-formula {
        display: inline-block;
        padding: 4px 6px;
        margin: 2px;
        background: #f8fafc;
        /* abu soft */
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
    }

    .ql-editor .ql-formula:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    /* Saat formula aktif diklik */
    .ql-editor .ql-formula.active {
        outline: 2px solid #3b82f6;
        /* biru */
        background: #eff6ff;
    }

    /* Styling hasil render KaTeX biar lebih clean */
    .ql-editor .ql-formula .katex {
        font-size: 1.05em;
        /* sedikit lebih besar */
        color: #1e293b;
        /* abu gelap */
    }

    .ql-editor .ql-formula .katex .mord {
        margin: 0 2px;
        /* kasih jarak antar simbol */
    }
</style>


<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="">Manajemen Soal</h5>
                <div class="d-flex gap-2 flex-wrap">
                    <!-- Tombol Global -->
                    <button type="button" onclick="addQuestion()" class="btn btn-primary btn-sm">
                        <i data-feather="plus-circle"></i> Tambah Soal
                    </button>
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i data-feather="upload"></i>
                    </button>
                    <a href="<?= base_url('panel/banksoal/soal/export/') . $bank_soal_id ?>" target="_blank" class="btn btn-success btn-sm">
                        <i data-feather="download"></i>
                    </a>
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="printDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i data-feather="printer"></i> Cetak
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="printDropdown">
                            <li>
                                <a class="dropdown-item" href="<?= base_url('panel/banksoal/soal/print/') . $bank_soal_id ?>" target="_blank">
                                    <i data-feather="file-text" class="me-1"></i> Tanpa Kunci Jawaban
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('panel/banksoal/soal/print/') . $bank_soal_id . '?kunci=1' ?>" target="_blank">
                                    <i data-feather="check-square" class="me-1"></i> Dengan Kunci Jawaban
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- Tombol Refresh -->
                    <button type="button" id="refreshList" class="btn btn-outline-secondary btn-sm">
                        <i data-feather="refresh-cw"></i>
                    </button>
                </div>
            </div>

            <!-- Search -->
            <div class="input-group mb-3">
                <span class="input-group-text bg-white"><i data-feather="search"></i></span>
                <input type="text" id="searchBox" class="form-control form-control-sm" placeholder="Cari soal atau topik...">
            </div>

            <!-- Question List -->
            <div id="questionList" class="d-flex flex-column gap-2">
                <!-- Soal akan dimuat di sini via JS -->
            </div>

            <!-- Pagination -->
            <nav>
                <ul id="pagination" class="pagination justify-content-center mt-4"></ul>
            </nav>

        </div>
    </div>
</div>

<!-- Modal Import Soal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="post" action="<?= base_url('panel/banksoal/soal/import') ?>" enctype="multipart/form-data">
            <input type="hidden" name="bank_soal_id" value="<?= esc($bank_soal_id) ?>">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel"><i class="bi bi-upload"></i> Import Soal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="file" class="form-label">Masukan File</label>
                    <input type="file" class="form-control" name="file" id="file" accept=".xlsx,.xls,.docx" required>
                    <div class="form-text">Gunakan template sesuai format yang disediakan.</div>
                </div>
                <div class="alert alert-info p-2">
                    Download template excel:
                    <a href="<?= base_url('assets/template/template_import_soal.xlsx') ?>" target="_blank" class="fw-bold text-decoration-underline">Klik di sini</a>
                </div>
                <div class="alert alert-info p-2">
                    Download template word:
                    <a href="<?= base_url('assets/template/template_import_soal.docx') ?>" target="_blank" class="fw-bold text-decoration-underline">Klik di sini</a>
                </div>
                <div class="import-feedback"></div> <!-- Notifikasi muncul di sini -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Import</button>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="formulaModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="formulaModalTitle" class="modal-title">Tambah Rumus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input id="formula-input" class="form-control mb-3" placeholder="Contoh: x^2 + y^2 = z^2">
                <label for="formula-template"><b>📘 Template Rumus:</b></label>
                <select id="formula-template" class="form-select mb-3">
                    <option value="">-- Pilih Template --</option>
                    <optgroup label="📐 SD - Operasi Pangkat & Pecahan">
                        <!-- Pangkat -->
                        <option value="a^2">a² (pangkat dua)</option>
                        <option value="a^3">a³ (pangkat tiga)</option>
                        <option value="a^n">aⁿ (pangkat n)</option>
                        <option value="\sqrt{a}">√a (akar kuadrat)</option>
                        <option value="\sqrt[n]{a}">ⁿ√a (akar pangkat n)</option>

                        <!-- Pecahan -->
                        <option value="\tfrac{1}{2}">½</option>
                        <option value="\tfrac{3}{4}">¾</option>
                        <option value="\tfrac{a}{b}">a/b</option>
                        <option value="\dfrac{a+b}{c}">a+b/c</option>
                    </optgroup>
                    <!-- 📗 SD -->
                    <optgroup label="📗 SD - Bangun Datar & Ruang">
                        <option value="L = s^2">Luas Persegi</option>
                        <option value="K = 4s">Keliling Persegi</option>
                        <option value="L = p \times l">Luas Persegi Panjang</option>
                        <option value="K = 2(p+l)">Keliling Persegi Panjang</option>
                        <option value="L = \tfrac{1}{2} \times a \times t">Luas Segitiga</option>
                        <option value="L = \pi r^2">Luas Lingkaran</option>
                        <option value="K = 2 \pi r">Keliling Lingkaran</option>
                        <option value="V = s^3">Volume Kubus</option>
                        <option value="V = p \times l \times t">Volume Balok</option>
                        <option value="V = \tfrac{1}{3} \pi r^2 t">Volume Kerucut</option>
                        <option value="V = \tfrac{4}{3} \pi r^3">Volume Bola</option>
                    </optgroup>


                    <!-- 📘 SMP -->
                    <optgroup label="📘 SMP - Aljabar & Geometri">
                        <option value="x^2 + y^2 = z^2">Teorema Pythagoras</option>
                        <option value="a^2 - b^2 = (a-b)(a+b)">Selisih Kuadrat</option>
                        <option value="(a+b)^2 = a^2 + 2ab + b^2">Kuadrat Jumlah</option>
                        <option value="(a-b)^2 = a^2 - 2ab + b^2">Kuadrat Selisih</option>
                        <option value="(a+b)(a-b) = a^2 - b^2">Perkalian Binom</option>
                        <option value="y = mx + c">Persamaan Garis Lurus</option>
                        <option value="m = \frac{y_2 - y_1}{x_2 - x_1}">Gradien Garis</option>
                        <option value="L = \pi r s + \pi r^2">Luas Kerucut</option>
                        <option value="L = 2 \pi r (r+t)">Luas Tabung</option>
                        <option value="V = \pi r^2 t">Volume Tabung</option>
                    </optgroup>

                    <!-- 📕 SMA -->
                    <optgroup label="📕 SMA - Aljabar & Kalkulus">
                        <option value="\frac{-b \pm \sqrt{b^2 - 4ac}}{2a}">Rumus Kuadrat</option>
                        <option value="\Delta = b^2 - 4ac">Diskriminan Kuadrat</option>
                        <option value="\lim_{x \to a} f(x)">Limit Fungsi</option>
                        <option value="f'(x) = \lim_{h \to 0} \frac{f(x+h) - f(x)}{h}">Turunan Definisi</option>
                        <option value="\int f(x) dx">Integral Tak Tentu</option>
                        <option value="\int_a^b f(x) dx">Integral Tentu</option>
                        <option value="\sum_{i=1}^n i = \frac{n(n+1)}{2}">Sigma Penjumlahan</option>
                        <option value="S_n = \frac{n}{2}(a_1 + a_n)">Deret Aritmatika</option>
                        <option value="S_n = a \frac{r^n - 1}{r - 1}">Deret Geometri</option>
                    </optgroup>

                    <optgroup label="📕 SMA - Trigonometri">
                        <option value="\sin^2 \theta + \cos^2 \theta = 1">Identitas Trigonometri</option>
                        <option value="1 + \tan^2 \theta = \sec^2 \theta">Identitas Trigonometri 2</option>
                        <option value="\sin (A+B) = \sin A \cos B + \cos A \sin B">Rumus Sinus Jumlah</option>
                        <option value="\cos (A+B) = \cos A \cos B - \sin A \sin B">Rumus Cosinus Jumlah</option>
                        <option value="\tan (A+B) = \frac{\tan A + \tan B}{1 - \tan A \tan B}">Rumus Tangen Jumlah</option>
                        <option value="\sin \alpha = \frac{opposite}{hypotenuse}">Definisi Sinus</option>
                        <option value="\cos \alpha = \frac{adjacent}{hypotenuse}">Definisi Cosinus</option>
                        <option value="\tan \alpha = \frac{opposite}{adjacent}">Definisi Tangen</option>
                    </optgroup>

                    <optgroup label="📗 SMA - Matriks">
                        <option value="\begin{bmatrix} a & b \\ c & d \end{bmatrix}">Matriks 2x2</option>
                        <option value="\begin{bmatrix} a & b & c \\ d & e & f \\ g & h & i \end{bmatrix}">Matriks 3x3</option>
                        <option value="\begin{matrix} a & b \\ c & d \end{matrix}">Matriks tanpa bracket 2x2</option>
                        <option value="\begin{matrix} a & b & c \\ d & e & f \\ g & h & i \end{matrix}">Matriks tanpa bracket 3x3</option>
                        <option value="\det \begin{bmatrix} a & b \\ c & d \end{bmatrix}">Determinant 2x2</option>
                        <option value="\det \begin{bmatrix} a & b & c \\ d & e & f \\ g & h & i \end{bmatrix}">Determinant 3x3</option>
                        <option value="\text{Transpose: } A^T">Transpose Matriks</option>
                    </optgroup>


                    <!-- 📗 Fisika SMA -->
                    <optgroup label="⚡ SMA - Fisika Dasar">
                        <option value="F = ma">Hukum II Newton</option>
                        <option value="w = F \times s">Usaha</option>
                        <option value="P = \frac{w}{t}">Daya</option>
                        <option value="v = \frac{s}{t}">Kecepatan</option>
                        <option value="a = \frac{\Delta v}{\Delta t}">Percepatan</option>
                        <option value="p = m v">Momentum</option>
                        <option value="E_k = \tfrac{1}{2} m v^2">Energi Kinetik</option>
                        <option value="E_p = m g h">Energi Potensial</option>
                        <option value="E = mc^2">Relativitas (Einstein)</option>
                        <option value="V = IR">Hukum Ohm</option>
                    </optgroup>
                </select>


                <div id="formula-preview" class="p-3 border rounded bg-light text-center">
                    <em>Preview rumus akan muncul di sini...</em>
                </div>
            </div>
            <div class="modal-footer">
                <button id="formula-save" type="button" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
<?= $this->section('pagejs'); ?>
<!-- Quill full -->
<!-- Quill v1.3.6 (stabil dan kompatibel dengan image-resize) -->

<link href="<?= base_url() ?>src/plugins/src/editors/quill/quill.snow.css" rel="stylesheet" type="text/css" />

<script src="<?= base_url() ?>src/plugins/src/editors/quill/quill.min.js"></script>
<script src="<?= base_url() ?>src/plugins/src/editors/quill/katex.min.js"></script>
<!-- Image Resize Module untuk Quill v1 -->
<!-- Gunakan versi perbaikan image resize -->
<script src="<?= base_url() ?>src/plugins/src/editors/quill/image-resize.min.js"></script>



<script>
    const BlockEmbed = Quill.import('blots/block/embed');

    class AudioBlot extends BlockEmbed {
        static create(value) {
            const node = super.create();
            node.setAttribute('controls', true);
            node.setAttribute('src', value);
            node.setAttribute('style', 'width:50%;');
            return node;
        }

        static value(node) {
            return node.getAttribute('src');
        }
    }

    AudioBlot.blotName = 'audio';
    AudioBlot.tagName = 'audio';

    Quill.register(AudioBlot);

    class VideoBlot extends BlockEmbed {
        static create(value) {
            const node = super.create();
            if (typeof value === 'string') {
                node.setAttribute('src', value);
                node.setAttribute('controls', true);
                node.setAttribute('style', 'width:100%;'); // default
            } else if (typeof value === 'object') {
                node.setAttribute('src', value.src);
                node.setAttribute('controls', true);
                node.setAttribute('style', value.style || 'width:100%;');
            }
            return node;
        }

        static value(node) {
            return node.getAttribute('src');
        }
    }


    VideoBlot.blotName = 'video';
    VideoBlot.tagName = 'video';

    Quill.register(VideoBlot);


    class CustomImageBlot extends BlockEmbed {
        static create(value) {
            const node = super.create();
            node.setAttribute('src', value.url);
            if (value.width) {
                node.setAttribute('width', value.width);
            }
            if (value.style) {
                node.setAttribute('style', value.style);
            }
            return node;
        }

        static value(node) {
            return {
                url: node.getAttribute('src'),
                width: node.getAttribute('width'),
                style: node.getAttribute('style'),
            };
        }
    }

    CustomImageBlot.blotName = 'image';
    CustomImageBlot.tagName = 'img';
    Quill.register(CustomImageBlot, true);
</script>




<script>
    const bankSoalId = "<?= $bank_soal_id ?>";
    let allQuestions = []; // Menyimpan semua soal
    let currentPage = 1;
    const perPage = 5; // jumlah soal per halaman

    // Ambil semua soal dari server
    function fetchQuestions() {
        $.get(`<?= base_url('panel/banksoal/soal/list/') ?>${bankSoalId}`, function(res) {
            if (res.status) {
                allQuestions = res.data;
                renderList();
            }
        });
    }

    // Render soal sesuai search & pagination
    function renderList() {
        const search = $('#searchBox').val().toLowerCase();
        const filtered = allQuestions.filter(soal => {
            const pertanyaan = soal.pertanyaan.toLowerCase();
            const topik = soal.nama_topik ? soal.nama_topik.toLowerCase() : '';
            return pertanyaan.includes(search) || topik.includes(search);
        });

        const totalPages = Math.ceil(filtered.length / perPage);
        if (currentPage > totalPages) currentPage = totalPages || 1;

        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        const pageData = filtered.slice(start, end);

        const html = pageData.map((soal, index) => renderSoalCard(soal, start + index)).join('');
        $('#questionList').html(html);

        renderPagination(totalPages);
    }

    // Render tombol pagination
    function renderPagination(totalPages) {
        if (totalPages <= 1) {
            $('#pagination').html('');
            return;
        }

        let html = '';
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="goPage(${currentPage - 1})">Prev</a>
             </li>`;

        for (let i = 1; i <= totalPages; i++) {
            html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="goPage(${i})">${i}</a>
                 </li>`;
        }

        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="goPage(${currentPage + 1})">Next</a>
             </li>`;

        $('#pagination').html(html);
    }

    // Pindah halaman
    function goPage(page) {
        currentPage = page;
        renderList();
    }

    // Event search
    $('#searchBox').on('input', function() {
        currentPage = 1; // reset ke halaman 1 saat search
        renderList();
    });

    // Panggil fetch awal
    fetchQuestions();

    function renderSoalCard(soal, index = null) {
        let pilihan = '';
        const jenisMap = {
            pg: 'Pilihan Ganda',
            mpg: 'Multiple Ganda',
            benar_salah: 'Benar / Salah',
            jodohkan: 'Jodohkan',
            esai: 'Esai',
            isian: 'Isian'
        };

        if (['pg', 'mpg'].includes(soal.jenis_soal)) {
            pilihan = '<div class="list-group">' + soal.opsi.map(p => {
                const isBenar = p.is_true == '1';
                let badgeBobot = 'bg-dark';
                if (p.bobot == 0) badgeBobot = 'bg-warning text-dark';
                else if (p.bobot < 0) badgeBobot = 'bg-danger';

                return `
                    <div class="list-group-item ${isBenar ? 'bg-light' : ''}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-start gap-2">
                                <span class="badge bg-${isBenar ? 'success' : 'secondary'} mt-1">${p.label}</span>
                                <div class="opsi-html">${p.teks}</div>
                            </div>
                            <span class="badge ${badgeBobot}">Bobot: ${p.bobot}</span>
                        </div>
                    </div>`;
            }).join('') + '</div>';
        } else if (soal.jenis_soal === 'benar_salah') {
            pilihan = '<div class="list-group">' + soal.opsi.map(p => {
                const benar = p.is_true == '1';
                let badgeBobot = 'bg-dark';
                if (p.bobot == 0) badgeBobot = 'bg-warning text-dark';
                else if (p.bobot < 0) badgeBobot = 'bg-danger';

                return `
                    <div class="list-group-item d-flex justify-content-between align-items-center ${benar ? 'bg-light' : ''}">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-${benar ? 'success' : 'secondary'}">${p.label}</span>
                            <span>${p.teks}</span>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-${benar ? 'success' : 'secondary'} me-1">
                                ${benar ? 'Benar' : 'Salah'}
                            </span>
                            <span class="badge ${badgeBobot}">Bobot: ${p.bobot}</span>
                        </div>
                    </div>`;
            }).join('') + '</div>';
        } else if (soal.jenis_soal === 'jodohkan') {
            pilihan = '<ul class="list-group">' + soal.opsi.map(p => {
                let badgeClass = 'bg-dark';
                if (p.bobot == 0) badgeClass = 'bg-warning text-dark';
                else if (p.bobot < 0) badgeClass = 'bg-danger';

                return `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <span class="fw-semibold">${p.teks}</span>
                        <span class="text-muted">⇄</span>
                        <span class="fw-semibold">${p.pasangan}</span>
                    </div>
                    <span class="badge ${badgeClass}">Bobot: ${p.bobot}</span>
                </li>`;
            }).join('') + '</ul>';
        } else if (soal.jenis_soal === 'esai') {
            pilihan = `
        <div class="alert alert-secondary mb-0">
            <strong>Jawaban:</strong><br>
            ${soal.jawaban || '<em>(Belum diisi)</em>'}
        </div>`;
        } else if (soal.jenis_soal === 'isian') {
            let daftarJawaban = soal.jawaban;
            if (typeof daftarJawaban === 'string') {
                try {
                    daftarJawaban = JSON.parse(soal.jawaban);
                } catch (e) {
                    daftarJawaban = [soal.jawaban];
                }
            }

            if (!Array.isArray(daftarJawaban)) {
                daftarJawaban = [daftarJawaban];
            }

            pilihan = `
        <div class="mb-2"><strong>Jawaban yang benar:</strong></div>
        <div class="d-flex flex-wrap gap-2">
            ${daftarJawaban.map(j => `<span class="badge bg-primary">${j}</span>`).join('')}
        </div>`;
        }



        return `
            <div class="card mb-3" data-id="${soal.id}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-title mb-0">Soal ${index !== null ? '#' + (index + 1) : ''}</h6>
                        <div>
                            <button onclick="editQuestion('${soal.id}')" class="btn btn-sm btn-info">Edit</button>
                            <button onclick="deleteQuestion('${soal.id}')" class="btn btn-sm btn-danger">Hapus</button>
                        </div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-flex align-items-center gap-3">
                            <span>
                                <i class="bi bi-ui-checks-grid text-primary me-1"></i>
                                <strong>Jenis:</strong> ${jenisMap[soal.jenis_soal] || soal.jenis_soal}
                            </span>
                            ${soal.nama_topik ? `
                            <span>
                                <i class="bi bi-tag text-success me-1"></i>
                                <strong>Topik:</strong> ${soal.nama_topik}
                            </span>` : ''}
                        </small>
                    </div>

                    <div class="mb-3">
                        <p class="mb-1"><strong>Pertanyaan:</strong></p>
                        <div class="border rounded p-2 bg-light">${soal.pertanyaan}</div>
                    </div>
                    ${pilihan}
                    <div class="text-end mt-3">
                        <small class="text-muted">Total Bobot: ${soal.bobot}</small>
                    </div>
                </div>
            </div>`;
    }



    function renderQuestions() {
        $.get(`<?= base_url('panel/banksoal/soal/list/') ?>${bankSoalId}`, function(res) {
            if (res.status) {
                const html = res.data.map((soal, index) => renderSoalCard(soal, index)).join('');
                $('#questionList').html(html);
            }
        });
    }

    function cancelEdit(id) {
        $.get(`<?= base_url('panel/banksoal/soal/detail/') ?>${id}`, function(res) {
            if (!res.status) return;
            const card = renderSoalCard(res.data);
            $(`[data-id='${id}']`).replaceWith(card);
        });
    }

    function renderOpsi(value = '', jenis = 'pg', bobot = 0, isTrue = false, opsiId = '') {
        if (jenis === 'jodohkan') {
            const [left = '', right = ''] = value.split('=>').map(s => s.trim());
            return `
        <div class="row mb-2 opsi-jodohkan align-items-center" data-id="${opsiId}">
            <div class="col-md-4">
                <input type="text" class="form-control form-control-sm pilihan-kiri" placeholder="Pernyataan" value="${left}">
            </div>
            <div class="col-md-1 text-center">⇄</div>
            <div class="col-md-4">
                <input type="text" class="form-control form-control-sm pilihan-kanan" placeholder="Pasangan" value="${right}">
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control form-control-sm opsi-bobot" value="${bobot || 1}" placeholder="Bobot">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-sm btn-danger" onclick="removeOpsi(this)">✕</button>
            </div>
        </div>`;
        }

        const uniqueId = 'opsi_' + Math.random().toString(36).substr(2, 9);
        let kunciInput = '';

        if (jenis === 'pg') {
            const nameGroup = 'pg_' + (opsiId || 'default');
            kunciInput = `
                <input type="radio" id="${uniqueId}" name="${nameGroup}" class="btn-check kunci-input" ${isTrue ? 'checked' : ''}>
                <label for="${uniqueId}" class="btn btn-outline-primary btn-sm me-2">✓</label>`;
        } else if (jenis === 'mpg') {
            kunciInput = `
            <input type="checkbox" id="${uniqueId}" class="btn-check kunci-input" ${isTrue ? 'checked' : ''}>
            <label for="${uniqueId}" class="btn btn-outline-success btn-sm me-2">✓</label>`;
        } else if (jenis === 'benar_salah') {
            const nameGroup = 'bs_' + (opsiId || uniqueId);
            bobot = bobot || 1;
            kunciInput = `
            <div class="btn-group" role="group">
                <input type="radio" class="btn-check kunci-input" name="${nameGroup}" id="${uniqueId}_benar" value="1" ${isTrue ? 'checked' : ''}>
                <label class="btn btn-outline-success btn-sm" for="${uniqueId}_benar">Benar</label>

                <input type="radio" class="btn-check kunci-input" name="${nameGroup}" id="${uniqueId}_salah" value="0" ${!isTrue ? 'checked' : ''}>
                <label class="btn btn-outline-danger btn-sm" for="${uniqueId}_salah">Salah</label>
            </div>`;
        }

        // Escape value agar aman untuk innerHTML
        const safeValue = value
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");

        return `
    <div class="row align-items-center mb-2 opsi-item" data-id="${opsiId}">
        <div class="col-auto">
            ${kunciInput}
        </div>
        <div class="col">
            <div class="quill-wrapper">
                <div class="quill-opsi-editor" data-value="${safeValue}" tabindex="0"></div>
            </div>
            <input type="hidden" class="pilihan-input" value="${safeValue}">
        </div>
        <div class="col-2">
            <input type="number" class="form-control form-control-sm opsi-bobot" value="${bobot}" placeholder="Bobot">
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-sm btn-danger" onclick="removeOpsi(this)">✕</button>
        </div>
    </div>`;
    }




    const daftarTopik = <?= json_encode($topik_list); ?>;

    function renderFormSoal(mode = 'create', soal = null) {
        const isEdit = mode === 'edit';
        const formId = isEdit ? 'editForm' : 'questionForm';
        const formAction = isEdit ? `<?= base_url('panel/banksoal/soal/update/') ?>${soal.id}` : `<?= base_url('panel/banksoal/soal/create') ?>`;
        const jenis = soal?.jenis_soal ?? 'pg';
        const pertanyaan = soal?.pertanyaan ?? '';
        const bobot = soal?.bobot ?? 1;
        const jawaban = soal?.jawaban ?? '';
        const opsi = soal?.opsi ?? [];
        const jawabanVisible = ['esai', 'isian'].includes(jenis);
        const topikId = soal?.topik_soal_id ?? '';
        const topikOptions = daftarTopik.map(t =>
            `<option value="${t.id}" ${topikId === t.id ? 'selected' : ''}>${t.nama}</option>`
        ).join('');



        let pilihanList = '';

        if (jenis === 'jodohkan') {
            pilihanList = opsi.map(p => `
            <div class="row mb-2 opsi-jodohkan align-items-center">
                <div class="col-md-4">
                    <input type="text" class="form-control form-control-sm pilihan-kiri" placeholder="Pernyataan" value="${p.teks}">
                </div>
                <div class="col-md-1 text-center">⇄</div>
                <div class="col-md-4">
                    <input type="text" class="form-control form-control-sm pilihan-kanan" placeholder="Pasangan" value="${p.pasangan}">
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control form-control-sm opsi-bobot" value="${p.bobot || 1}" placeholder="Bobot">
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeOpsi(this)">✕</button>
                </div>
            </div>`).join('');


        } else if (['pg', 'mpg', 'benar_salah'].includes(jenis)) {
            pilihanList = opsi.map(p => renderOpsi(p.teks, jenis, p.bobot ?? 0, p.is_true == '1')).join('');
        }


        const card = `
                <form id="${formId}" class="card mb-3" ${isEdit ? `data-id="${soal.id}"` : ''}>
                    <div class="card-body">
                        <h6 class="card-title">${isEdit ? 'Edit Soal' : 'Tambah Soal Baru'}</h6>
                    
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label>Topik Soal</label>
                                    <select name="topik_soal_id" class="form-control form-control-sm" required>
                                        <option value="">Pilih Topik</option>
                                        ${topikOptions}
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label>Jenis Soal</label>
                                    <select name="jenis_soal" class="form-control form-control-sm" onchange="togglePilihan(this)" required>
                                        <option value="pg" ${jenis === 'pg' ? 'selected' : ''}>Pilihan Ganda</option>
                                        <option value="mpg" ${jenis === 'mpg' ? 'selected' : ''}>Multiple PG</option>
                                        <option value="jodohkan" ${jenis === 'jodohkan' ? 'selected' : ''}>Jodohkan</option>
                                        <option value="benar_salah" ${jenis === 'benar_salah' ? 'selected' : ''}>Benar / Salah</option>
                                        <option value="esai" ${jenis === 'esai' ? 'selected' : ''}>Esai</option>
                                        <option value="isian" ${jenis === 'isian' ? 'selected' : ''}>Isian</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                       <div class="form-group mb-2">
                            <label>Pertanyaan</label>

                            <!-- Custom Toolbar -->
                            <div id="toolbar-pertanyaan">
                                <span class="ql-formats">
                                    <button class="ql-bold"></button>
                                    <button class="ql-italic"></button>
                                    <button class="ql-underline"></button>
                                    <button class="ql-strike"></button>
                                </span>
                                <span class="ql-formats">
                                    <button class="ql-image"></button>
                                    <button class="ql-video"></button>
                                    <!-- Tambahkan tombol audio manual di sini -->
                                    <button type="button" id="ql-audio" title="Upload Audio">🔈</button>
                                      <button class="ql-formula">∑</button> <!-- tombol rumus -->
                                </span>
                                <span class="ql-formats">
                                    <button class="ql-list" value="ordered"></button>
                                    <button class="ql-list" value="bullet"></button>
                                </span>
                                <span class="ql-formats">
                                    <!-- Tambahkan di sini -->
                                    <button class="ql-code-block" title="Code Block"></button>
                                    <button class="ql-clean"></button>
                                </span>
                            </div>


                            <!-- Quill Editor -->
                            <div class="form-control form-control-sm" id="editor-pertanyaan" ${isEdit ? `data-soal-id="${soal.id}"` : ''} style="min-height:120px;">${pertanyaan}</div>
                            <input type="hidden" name="pertanyaan">
                        </div>

                        
                        <div class="form-group mb-2 pilihan-container">
                            <label>Pilihan Jawaban</label>
                            <div id="pilihanList">${pilihanList}</div>
                            <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="addOpsi()">Tambah Opsi</button>
                        </div>
                       <div class="form-group mb-2" id="jawabanContainer" style="${jawabanVisible ? '' : 'display: none;'}">
                            <label>Jawaban</label>
                            ${
                                jenis === 'isian'
                                    ? `<select name="jawaban[]" id="jawaban_tags" class="form-control form-control-sm" multiple>
                                      
                                        </select>
                                        `
                                    : `<input type="text" name="jawaban" class="form-control form-control-sm" value="${jawaban}">`
                            }
                        </div>

                        <div class="form-group mb-2">
                            <label>Bobot Total</label>
                            <input type="number" name="bobot_total" id="bobot_total" class="form-control form-control-sm" value="${bobot}" readonly>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-light-dark" onclick="${isEdit ? `cancelEdit('${soal.id}')` : 'renderQuestions()'}">Batal</button>
                            <button type="submit" class="btn btn-primary">${isEdit ? 'Update' : 'Simpan'}</button>
                        </div>
                    </div>
                </form>`;
        setTimeout(() => {

            initQuillPertanyaan();
            initQuillOpsi();
            // if (jenis === 'isian') {
            //     initJawabanTagsSelect(jawabanArray);
            // }
        }, 10);
        return card;
    }

    function initJawabanTagsSelect(jawabanArray = []) {
        const el = document.querySelector('#jawaban_tags');
        if (!el) return;

        if (el.tomselect) el.tomselect.destroy();

        el.innerHTML = ''; // Bersihkan sebelum append

        jawabanArray.forEach(val => {
            const opt = document.createElement('option');
            opt.value = val;
            opt.text = val;
            opt.selected = true;
            el.appendChild(opt);
        });

        const ts = new TomSelect(el, {
            create: true,
            persist: false,
            createOnBlur: true
        });

        ts.setValue(jawabanArray);
        console.log('✅ setValue dengan:', jawabanArray);
        console.trace('Jejak pemanggilan initJawabanTagsSelect');

    }

    let currentFormulaEl = null;
    let currentQuill = null;
    let formulaModal;

    function initFormulaModal() {
        const modalEl = document.getElementById('formulaModal');
        const input = document.getElementById('formula-input');
        const preview = document.getElementById('formula-preview');
        const select = document.getElementById('formula-template');
        const saveBtn = document.getElementById('formula-save');
        const title = document.getElementById('formulaModalTitle');

        formulaModal = new bootstrap.Modal(modalEl);

        // realtime preview
        input.addEventListener('input', () => {
            try {
                katex.render(input.value, preview, {
                    throwOnError: false
                });
            } catch {
                preview.innerHTML = '<span class="text-danger">LaTeX salah!</span>';
            }
        });

        // pilih template
        select.addEventListener('change', () => {
            if (!select.value) return;
            input.value = select.value;
            katex.render(select.value, preview, {
                throwOnError: false
            });
        });

        // simpan formula
        saveBtn.addEventListener('click', () => {
            const latex = input.value.trim();
            if (!latex) {
                alert('LaTeX tidak boleh kosong!');
                return;
            }

            if (!currentQuill) return;

            if (currentFormulaEl) {
                // edit existing formula
                currentFormulaEl.setAttribute('data-value', latex);
                currentFormulaEl.innerHTML = katex.renderToString(latex, {
                    throwOnError: false
                });
            } else {
                // insert baru
                const range = currentQuill.getSelection(true);
                currentQuill.insertEmbed(range.index, 'formula', latex, Quill.sources.USER);
                currentQuill.setSelection(range.index + 1);
            }

            formulaModal.hide();
        });
    }

    // buka modal formula
    function openFormulaModal(quillInstance, formulaEl = null) {
        currentQuill = quillInstance;
        currentFormulaEl = formulaEl;

        const title = document.getElementById('formulaModalTitle');
        const input = document.getElementById('formula-input');
        const preview = document.getElementById('formula-preview');
        const select = document.getElementById('formula-template');

        title.innerText = formulaEl ? 'Edit Rumus' : 'Tambah Rumus';
        input.value = formulaEl?.getAttribute('data-value') || '';
        select.value = '';

        preview.innerHTML = '<em>Preview rumus akan muncul di sini...</em>';
        if (formulaEl?.getAttribute('data-value')) {
            katex.render(formulaEl.getAttribute('data-value'), preview, {
                throwOnError: false
            });
        }

        formulaModal.show();
    }

    // ==================== Quill Pertanyaan ====================
    function initQuillPertanyaan() {
        if (typeof Quill === 'undefined') {
            console.error('Quill belum dimuat!');
            return;
        }

        if (typeof window.ImageResize !== 'undefined' && !Quill.imports['modules/imageResize']) {
            Quill.register('modules/imageResize', window.ImageResize);
        }

        const elEditor = document.querySelector('#editor-pertanyaan');
        const elToolbar = document.querySelector('#toolbar-pertanyaan');
        const hiddenInput = document.querySelector('input[name="pertanyaan"]');
        if (!elEditor || elEditor.classList.contains('ql-container')) return;

        const quillMain = new Quill(elEditor, {
            theme: 'snow',
            modules: {
                toolbar: {
                    container: elToolbar,
                    handlers: {
                        image: () => uploadFile(quillMain, 'image'),
                        video: () => uploadFile(quillMain, 'video'),
                        formula: function() {
                            openFormulaModal(this.quill);
                        }
                    }
                },
                imageResize: {
                    modules: ['Resize', 'DisplaySize', 'Toolbar']
                },
            },
            formats: [
                'bold', 'italic', 'underline', 'strike',
                'image', 'video', 'audio',
                'list', 'bullet', 'link', 'code-block',
                'formula'
            ]
        });

        const audioBtn = document.querySelector('#ql-audio');
        if (audioBtn) audioBtn.addEventListener('click', () => uploadFile(quillMain, 'audio'));

        function syncHiddenInput() {
            hiddenInput.value = quillMain.root.innerHTML;
        }
        syncHiddenInput();
        quillMain.on('text-change', syncHiddenInput);

        // media observer
        function getCurrentMedia() {
            const img = [...elEditor.querySelectorAll('img')].map(el => el.src);
            const vid = [...elEditor.querySelectorAll('video')].map(el => el.src);
            const aud = [...elEditor.querySelectorAll('audio')].map(el => {
                const source = el.querySelector('source');
                return source ? source.src : el.src;
            });
            return [...img, ...vid, ...aud].filter(Boolean);
        }
        let lastMedia = getCurrentMedia();
        const observer = new MutationObserver(() => {
            const currentMedia = getCurrentMedia();
            const deleted = lastMedia.filter(url => !currentMedia.includes(url));
            const base = '<?= base_url() ?>';
            deleted.forEach(url => {
                if (url.startsWith(base + 'uploads/images/') || url.startsWith(base + 'uploads/audio/') || url.startsWith(base + 'uploads/video/')) {
                    const relativePath = url.replace(base, '');
                    fetch('<?= base_url('panel/banksoal/soal/delete-file') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            path: relativePath
                        })
                    }).then(res => res.json()).then(json => console.log('[RESPON DELETE]', json)).catch(console.error);
                }
            });
            lastMedia = currentMedia;
        });
        observer.observe(elEditor.querySelector('.ql-editor'), {
            childList: true,
            subtree: true
        });

        document.addEventListener('mousedown', e => {
            const overlay = document.querySelector('.ql-image-resize-module');
            if (overlay && !e.target.closest('.ql-editor')) overlay.remove();
        });

        // klik formula untuk edit
        elEditor.addEventListener('click', e => {
            const formulaEl = e.target.closest('span.ql-formula');
            if (!formulaEl) return;
            openFormulaModal(quillMain, formulaEl);
        });
    }

    // ==================== Quill Opsi ====================
    function initQuillOpsi() {
        $('.quill-opsi-editor').each(function(i, el) {
            if ($(el).hasClass('ql-container')) return;

            const toolbarId = 'toolbar-opsi-' + i;
            const toolbar = document.createElement('div');
            toolbar.id = toolbarId;
            toolbar.classList.add('quill-opsi-toolbar', 'mb-2');
            toolbar.innerHTML = `
            <span class="ql-formats">
                <button class="ql-bold"></button>
                <button class="ql-italic"></button>
                <button class="ql-underline"></button>
            </span>
            <span class="ql-formats">
                <button class="ql-image"></button>
                <button class="ql-video"></button>
                <button class="ql-formula"></button>
                <button id="ql-audio-${i}" type="button">🎵</button>
            </span>
        `;
            $(el).before(toolbar);

            const quillOpsi = new Quill(el, {
                theme: 'snow',

                modules: {
                    toolbar: {
                        container: '#' + toolbarId,
                        handlers: {
                            image: () => uploadFile(quillOpsi, 'image'),
                            video: () => uploadFile(quillOpsi, 'video'),
                            formula: function() {
                                openFormulaModal(this.quill);
                            }
                        }
                    },
                    imageResize: {
                        modules: ['Resize', 'DisplaySize', 'Toolbar']
                    },
                },
                formats: [
                    'bold', 'italic', 'underline', 'strike',
                    'image', 'video', 'audio',
                    'list', 'bullet', 'link', 'code-block',
                    'formula'
                ]
            });

            const audioBtn = document.querySelector('#ql-audio-' + i);
            if (audioBtn) audioBtn.addEventListener('click', () => uploadFile(quillOpsi, 'audio'));

            // load nilai awal
            const val = $(el).attr('data-value') || '';
            if (val) quillOpsi.clipboard.dangerouslyPasteHTML(val);

            // sinkron ke input hidden
            const hiddenInput = $(el).closest('.opsi-item').find('.pilihan-input');

            function syncOpsiInput() {
                hiddenInput.val(quillOpsi.root.innerHTML.trim());
            }
            syncOpsiInput();
            quillOpsi.on('text-change', syncOpsiInput);

            // klik formula untuk edit
            el.addEventListener('click', e => {
                const formulaEl = e.target.closest('span.ql-formula');
                if (!formulaEl) return;
                openFormulaModal(quillOpsi, formulaEl);
            });

            // media observer per editor
            function getCurrentMedia() {
                const img = [...el.querySelectorAll('img')].map(e => e.src);
                const vid = [...el.querySelectorAll('video')].map(e => e.src);
                const aud = [...el.querySelectorAll('audio')].map(e => {
                    const src = e.querySelector('source');
                    return src ? src.src : e.src;
                });
                return [...img, ...vid, ...aud].filter(Boolean);
            }
            let lastMedia = getCurrentMedia();
            const observer = new MutationObserver(() => {
                const currentMedia = getCurrentMedia();
                const deleted = lastMedia.filter(url => !currentMedia.includes(url));
                const base = '<?= base_url() ?>';
                deleted.forEach(url => {
                    if (url.startsWith(base + 'uploads/images/') || url.startsWith(base + 'uploads/audio/') || url.startsWith(base + 'uploads/video/')) {
                        const relativePath = url.replace(base, '');
                        fetch('<?= base_url('panel/banksoal/soal/delete-file') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams({
                                path: relativePath
                            })
                        }).then(res => res.json()).then(json => console.log('[RESPON DELETE]', json)).catch(console.error);
                    }
                });
                lastMedia = currentMedia;
            });
            observer.observe(el, {
                childList: true,
                subtree: true
            });
        });
    }




    function uploadFile(quill, type) {
        const input = document.createElement('input');
        input.setAttribute('type', 'file');

        // Batasi jenis file yang dapat diunggah
        if (type === 'image') input.setAttribute('accept', 'image/*');
        if (type === 'audio') input.setAttribute('accept', 'audio/*');
        if (type === 'video') input.setAttribute('accept', 'video/*');

        input.click();

        input.onchange = async () => {
            const file = input.files[0];
            if (!file) return;

            const soalId = document.querySelector('#editor-pertanyaan')?.dataset.soalId || null;

            const formData = new FormData();
            formData.append(type, file);
            if (soalId) formData.append('used_in_soal', soalId);

            try {
                const res = await fetch(`<?= base_url('panel/banksoal/soal/upload/') ?>${type}`, {
                    method: 'POST',
                    body: formData
                });

                const json = await res.json();
                if (!json.status) throw new Error(json.message);

                const range = quill.getSelection();
                if (!range) return;

                // Ambil path relatif dari URL (hilangkan domain/ip)
                const relativeUrl = json.url.replace(/^https?:\/\/[^/]+/, '');

                if (type === 'image') {
                    quill.insertEmbed(range.index, 'image', {
                        url: relativeUrl,
                        width: '50%',
                        style: 'cursor: nwse-resize;'
                    });
                } else if (type === 'video') {
                    quill.insertEmbed(range.index, 'video', {
                        src: relativeUrl,
                        style: 'width:50%; height:auto;'
                    });
                } else if (type === 'audio') {
                    quill.insertEmbed(range.index, 'audio', relativeUrl);
                }

                quill.setSelection(range.index + 1);
            } catch (err) {
                alert('Upload gagal: ' + err.message);
            }
        };
    }




    function addQuestion() {

        // Hapus semua form aktif
        $('#questionList form').remove(); // Hapus form aktif lain

        const form = $(renderFormSoal('create')).addClass('form-aktif');
        $('#questionList').prepend(form);

        togglePilihan($('#questionForm select[name="jenis_soal"]'));


        $('#questionForm').on('submit', function(e) {
            e.preventDefault();
            const jenis = $(this).find('select[name="jenis_soal"]').val();
            const form = $(this);
            const data = form.serializeArray();
            const pilihan = [];

            // Tambahkan bank_soal_id ke data
            data.push({
                name: 'bank_soal_id',
                value: bankSoalId
            });

            if (jenis === 'jodohkan') {
                $('#pilihanList .opsi-jodohkan').each(function() {
                    const kiri = $(this).find('.pilihan-kiri').val();
                    const kanan = $(this).find('.pilihan-kanan').val();
                    const bobot = $(this).find('.opsi-bobot').val() || 0;
                    if (kiri && kanan) {
                        pilihan.push({
                            teks: kiri,
                            pasangan: kanan,
                            bobot_opsi: parseFloat(bobot)
                        });
                    }
                });
            } else if (['pg', 'mpg'].includes(jenis)) {
                $('#pilihanList .opsi-item').each(function() {
                    const teks = $(this).find('.pilihan-input').val();
                    const benar = $(this).find('.kunci-input').is(':checked');
                    const bobot = $(this).find('.opsi-bobot').val() || 0;

                    if (teks) {
                        pilihan.push({
                            teks,
                            benar: benar ? 1 : 0,
                            bobot_opsi: parseFloat(bobot)
                        });
                    }
                });

            } else if (jenis === 'benar_salah') {
                $('#pilihanList .opsi-item').each(function() {
                    const opsiId = $(this).data('id') || null;
                    const teks = $(this).find('.pilihan-input').val();
                    const selectedVal = $(this).find('.kunci-input:checked').val(); // 1 atau 0
                    const bobot = $(this).find('.opsi-bobot').val() || 1;

                    if (teks) {
                        pilihan.push({
                            id: opsiId,
                            teks,
                            benar: selectedVal ? parseInt(selectedVal) : 0, // simpan 1/0
                            bobot_opsi: parseFloat(bobot)
                        });
                    }
                });
            }

            if (jenis === 'isian') {
                const jawabanFinal = [...new Set($('#jawaban_tags').val() || [])];
                data.push({
                    name: 'jawaban',
                    value: JSON.stringify(jawabanFinal)
                });
            } else if (jenis === 'esai') {
                const jawaban = form.find('input[name="jawaban"]').val();
                data.push({
                    name: 'jawaban',
                    value: jawaban
                });
            }
            if (['pg', 'mpg', 'benar_salah', 'jodohkan'].includes(jenis)) {
                data.push({
                    name: 'pilihan',
                    value: JSON.stringify(pilihan)
                });
            }

            $.post(`<?= base_url('panel/banksoal/soal/create') ?>`, data, function(res) {
                if (res.status) {
                    Snackbar.show({
                        text: res.message
                    });
                    renderQuestions();
                } else {
                    alert('Error: ' + JSON.stringify(res.message));
                }
            }, 'json');
        });

    }

    function editQuestion(id) {
        // 1. Hapus semua form edit yang sudah ada
        $('form[data-id]').each(function() {
            const existingId = $(this).attr('data-id');
            if (existingId !== id) {
                cancelEdit(existingId); // panggil fungsi cancelEdit untuk kembalikan tampilan
            }
        });
        $.get(`<?= base_url('panel/banksoal/soal/detail/') ?>${id}`, function(res) {
            if (!res.status) return alert(res.message);


            const form = $(renderFormSoal('edit', res.data)).addClass('form-aktif');
            $(`[data-id='${id}']`).replaceWith(form);



            setTimeout(() => {
                togglePilihan($('#editForm select[name="jenis_soal"]'), true);

                requestAnimationFrame(() => {
                    const jawaban = res.data.jawaban;
                    const jawabanArray = Array.isArray(jawaban) ?
                        jawaban :
                        (typeof jawaban === 'string' && jawaban.startsWith('[')) ?
                        JSON.parse(jawaban) : [jawaban];

                    initJawabanTagsSelect(jawabanArray);
                });

            }, 30);


            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const jenis = form.find('select[name="jenis_soal"]').val();
                const data = form.serializeArray();
                const pilihan = [];

                if (jenis === 'jodohkan') {
                    form.find('#pilihanList .opsi-jodohkan').each(function() {
                        const opsiId = $(this).data('id') || null;
                        const kiri = $(this).find('.pilihan-kiri').val();
                        const kanan = $(this).find('.pilihan-kanan').val();
                        const bobot = $(this).find('.opsi-bobot').val() || 0;
                        if (kiri && kanan) pilihan.push({
                            teks: kiri,
                            pasangan: kanan,
                            bobot_opsi: parseFloat(bobot)
                        });
                    });
                } else if (['pg', 'mpg'].includes(jenis)) {
                    $('#pilihanList .opsi-item').each(function() {
                        const opsiId = $(this).data('id') || null;
                        const teks = $(this).find('.pilihan-input').val();
                        const benar = $(this).find('.kunci-input').is(':checked');
                        const bobot = $(this).find('.opsi-bobot').val() || 0;

                        if (teks) {
                            pilihan.push({
                                id: opsiId, // kirim id-nya
                                teks,
                                benar: benar ? 1 : 0,
                                bobot_opsi: parseFloat(bobot)
                            });
                        }
                    });


                } else if (jenis === 'benar_salah') {
                    form.find('#pilihanList .opsi-item').each(function() {
                        const opsiId = $(this).data('id') || null;
                        const teks = $(this).find('.pilihan-input').val();
                        const jawaban = $(this).find('.kunci-input:checked').val(); // 1 atau 0
                        const bobot = $(this).find('.opsi-bobot').val() || 1;

                        if (teks) {
                            pilihan.push({
                                id: opsiId,
                                teks,
                                benar: jawaban ? parseInt(jawaban) : 0, // simpan 1/0
                                bobot_opsi: parseFloat(bobot)
                            });
                        }
                    });
                }



                if (jenis === 'isian') {
                    const jawabanFinal = [...new Set($('#jawaban_tags').val() || [])];
                    data.push({
                        name: 'jawaban',
                        value: JSON.stringify(jawabanFinal)
                    });
                } else if (jenis === 'esai') {
                    const jawaban = form.find('input[name="jawaban"]').val();
                    data.push({
                        name: 'jawaban',
                        value: jawaban
                    });
                }

                data.push({
                    name: 'pilihan',
                    value: JSON.stringify(pilihan)
                });

                $.post(`<?= base_url('panel/banksoal/soal/update/') ?>${id}`, data, function(res) {
                    if (res.status) {
                        Snackbar.show({
                            text: res.message
                        });
                        renderQuestions();
                    } else {
                        alert('Error: ' + JSON.stringify(res.message));
                    }
                }, 'json');
            });
        });

    }



    function removeOpsi(btn) {
        $(btn).closest('.opsi-item, .opsi-jodohkan').remove();
    }

    function togglePilihan(select, isEdit = false) {
        const jenis = $(select).val();
        const container = $(select).closest('.card-body').find('.pilihan-container');
        const jawabanContainer = $(select).closest('.card-body').find('#jawabanContainer');
        const pilihanList = $(select).closest('.card-body').find('#pilihanList');

        const isFreeBobot = ['esai', 'isian'].includes(jenis);
        $('#bobot_total').prop('readonly', !isFreeBobot);
        if (['pg', 'mpg', 'benar_salah'].includes(jenis)) {
            container.show();
            jawabanContainer.hide();

            if (!isEdit) {
                pilihanList.html('');
                const opsi = jenis === 'benar_salah' ? ['Benar', 'Salah'] : [''];
                opsi.forEach(val => {
                    pilihanList.append(renderOpsi(val, jenis));
                });
            }

        } else if (jenis === 'jodohkan') {
            container.show();
            jawabanContainer.hide();

            if (!isEdit) {
                pilihanList.html('');
                pilihanList.append(`<div class="alert alert-info">Format: "Pernyataan => Pasangan"</div>`);
                addOpsi();
                addOpsi();
            }

        } else {
            container.hide();
            // 🔁 Perbarui isi jawabanContainer sesuai jenisnya
            if (jenis === 'isian') {
                jawabanContainer
                    .show()
                    .html(`
                    <label>Jawaban (Kemungkinan Jawaban bisa Lebih dari 1 kunci jawaban)</label>
                    <select name="jawaban[]" id="jawaban_tags" class="form-control form-control-sm" multiple></select>
                `);
                // Inisialisasi TomSelect
                new TomSelect('#jawaban_tags', {
                    persist: false,
                    createOnBlur: true,
                    create: true,

                });
            } else {
                jawabanContainer
                    .show()
                    .html(`
                    <label>Jawaban</label>
                    <input type="text" name="jawaban" class="form-control form-control-sm" />
                `);
            }

        }
        initQuillOpsi();
    }


    function addOpsi(btn = null) {
        const form = btn ? $(btn).closest('form') : $('#questionForm').length ? $('#questionForm') : $('#editForm');
        const jenis = form.find('select[name="jenis_soal"]').val();
        const pilihanList = form.find('#pilihanList');

        pilihanList.append(renderOpsi('', jenis));
        hitungBobotTotal(); // panggil setelah nambah opsi
        initQuillOpsi();
    }


    function deleteQuestion(id) {
        Swal.fire({
            title: 'Yakin hapus soal ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        }).then(result => {
            if (result.isConfirmed) {
                $.post(`<?= base_url('panel/banksoal/soal/delete/') ?>${id}`, function(res) {
                    Snackbar.show({
                        text: res.message
                    });
                    renderQuestions();
                });
            }
        });
    }

    // Untuk checkbox (mpg, benar/salah)
    $(document).on('change', 'input[type="checkbox"].kunci-input', function() {
        const row = $(this).closest('.opsi-item');
        const bobotInput = row.find('.opsi-bobot');
        bobotInput.val(this.checked ? 1 : 0);
        hitungBobotTotal();
    });

    // Untuk radio (pg)
    $(document).on('change', 'input[type="radio"].kunci-input', function() {
        const name = $(this).attr('name'); // biasanya 'kunci'
        const group = $(`input[type="radio"][name="${name}"]`);

        group.each(function() {
            const row = $(this).closest('.opsi-item');
            row.find('.opsi-bobot').val(0);
        });

        const selectedRow = $(this).closest('.opsi-item');
        selectedRow.find('.opsi-bobot').val(1);
        hitungBobotTotal();
    });


    function hitungBobotTotal() {
        let total = 0;
        $('.opsi-bobot').each(function() {
            const val = parseFloat($(this).val()) || 0;
            if (val > 0) total += val;
        });
        $('#bobot_total').val(total);
    }

    // Jalankan saat form dibuka
    $(document).on('input', '.opsi-bobot', function() {
        hitungBobotTotal();
    });


    $(document).ready(function() {
        initFormulaModal(); // ✅ inisialisasi modal global sekali
        renderQuestions();
    });

    $(document).ready(function() {
        $('#importModal').on('shown.bs.modal', function() {
            $(this).find('.import-feedback').html('');
        });
        $('#importModal form').on('submit', function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);
            const submitBtn = $(form).find('button[type="submit"]');
            const feedback = $('#importModal .import-feedback');

            // Reset feedback sebelumnya
            feedback.html('');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Importing...');

            $.ajax({
                url: $(form).attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(res) {
                    let html = '';

                    if (res.status) {
                        html += `<div class="alert alert-success mb-2">${res.message || 'Soal berhasil diimpor.'}</div>`;

                        if (Array.isArray(res.gagal) && res.gagal.length > 0) {
                            html += `<div class="alert alert-warning mb-2"><strong>${res.gagal.length} soal gagal diimpor:</strong><ul class="mb-0">`;
                            res.gagal.forEach(item => {
                                html += `<li>Baris ${item.baris}: ${item.alasan}</li>`;
                            });
                            html += '</ul></div>';
                        }

                        form.reset(); // Kosongkan file input
                        renderQuestions(); // Refresh daftar soal jika tersedia
                    } else {
                        html = `<div class="alert alert-danger mb-2">${res.message || 'Gagal mengimpor soal.'}</div>`;
                    }

                    feedback.html(html);
                },
                error: function(xhr) {
                    feedback.html(`<div class="alert alert-danger mb-2">Terjadi kesalahan server saat mengunggah file.</div>`);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="bi bi-upload"></i> Import');
                }
            });
        });

    });
</script>
<?= $this->endSection(); ?>