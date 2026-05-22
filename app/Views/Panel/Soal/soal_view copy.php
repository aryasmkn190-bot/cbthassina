<?= $this->extend('Layout/main'); ?>
<?= $this->section('content'); ?>

<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        <div class="layout-top-spacing">
            <div class="widget widget-three">
                <div class="widget-heading d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h5 class="mb-1">Manajemen Soal</h5>
                        <small class="text-muted">
                            <i class="bi bi-journal-text me-1"></i>
                            <strong>Bank Soal:</strong> <?= esc($bank_soal['nama']) ?>
                        </small>
                    </div>
                    <button type="button" onclick="addQuestion()" class="btn btn-primary">
                        <i data-feather="plus-circle"></i> Tambah Soal
                    </button>
                </div>

                <div class="widget-content" id="questionList">
                    <!-- Soal akan dimuat di sini via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>
<?= $this->section('pagejs'); ?>
<script>
    const bankSoalId = "<?= $bank_soal_id ?>";

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
            pilihan = '<div class="list-group">' + soal.opsi.map(p =>
                `<div class="list-group-item d-flex justify-content-between align-items-center ${p.is_true == '1' ? 'bg-light' : ''}">
                <div>
                    <span class="badge bg-${p.is_true == '1' ? 'success' : 'secondary'} me-2">${p.label}</span>
                    ${p.teks}
                </div>
                ${p.is_true == '1' ? '<i class="text-success fw-bold">✔</i>' : ''}
            </div>`
            ).join('') + '</div>';
        } else if (soal.jenis_soal === 'benar_salah') {
            pilihan = '<div class="list-group">' + soal.opsi.map(p =>
                `<div class="list-group-item d-flex justify-content-between align-items-center ${p.is_true == '1' ? 'bg-light' : ''}">
                <div>
                    <span class="badge bg-${p.is_true == '1' ? 'success' : 'secondary'} me-2">${p.label}</span>
                    ${p.teks}
                </div>
                <span class="badge bg-${p.is_true == '1' ? 'success' : 'secondary'}">${p.is_true == '1' ? 'Benar' : 'Salah'}</span>
            </div>`
            ).join('') + '</div>';
        } else if (soal.jenis_soal === 'jodohkan') {
            pilihan = '<ul class="list-group">' + soal.opsi.map(p =>
                `<li class="list-group-item d-flex justify-content-between align-items-center">
                <span>${p.teks}</span>
                <span class="text-muted">⇄</span>
                <span>${p.pasangan}</span>
            </li>`
            ).join('') + '</ul>';
        } else if (['esai', 'isian'].includes(soal.jenis_soal)) {
            pilihan = `
        <div class="alert alert-secondary mb-0">
            <strong>Jawaban:</strong><br>
            ${soal.jawaban || '<em>(Belum diisi)</em>'}
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
                <small class="text-muted">Bobot: ${soal.bobot}</small>
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

    function renderOpsi(value = '', jenis = 'pg', bobot = 0, isTrue = false) {
        if (jenis === 'jodohkan') {
            const [left = '', right = ''] = value.split('=>').map(s => s.trim());
            return `
            <div class="row mb-2 opsi-jodohkan align-items-center">
                <div class="col-md-5">
                    <input type="text" class="form-control pilihan-kiri" placeholder="Pernyataan" value="${left}">
                </div>
                <div class="col-md-1 text-center">⇄</div>
                <div class="col-md-5">
                    <input type="text" class="form-control pilihan-kanan" placeholder="Pasangan" value="${right}">
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeOpsi(this)">✕</button>
                </div>
            </div>`;
        }
        const uniqueId = 'opsi_' + Math.random().toString(36).substr(2, 9);
        const checkedAttr = isTrue ? 'checked' : '';
        let kunciInput = '';
        if (jenis === 'pg') {
            kunciInput = `
            <input type="radio" id="${uniqueId}" name="kunci" class="btn-check kunci-input" ${checkedAttr}>
            <label for="${uniqueId}" class="btn btn-outline-primary btn-sm me-2">✓</label>`;
        } else if (jenis === 'mpg' || jenis === 'benar_salah') {
            kunciInput = `
            <input type="checkbox" id="${uniqueId}" class="btn-check kunci-input" ${checkedAttr}>
            <label for="${uniqueId}" class="btn btn-outline-success btn-sm me-2">✓</label>`;
        }

        return `
        <div class="row align-items-center mb-2 opsi-item">
            <div class="col-auto">
                ${kunciInput}
            </div>
            <div class="col">
                <textarea class="form-control pilihan-input" rows="2" placeholder="Teks opsi">${value}</textarea>
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
            <div class="row mb-2 opsi-jodohkan">
                <div class="col-5">
                    <input type="text" class="form-control pilihan-kiri" placeholder="Pernyataan" value="${p.teks}">
                </div>
                <div class="col-1 text-center">⇄</div>
                <div class="col-5">
                    <input type="text" class="form-control pilihan-kanan" placeholder="Pasangan" value="${p.pasangan}">
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-danger" onclick="removeOpsi(this)">✕</button>
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
                    <select name="topik_soal_id" class="form-control" required>
                        <option value="">Pilih Topik</option>
                        ${topikOptions}
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-2">
                    <label>Jenis Soal</label>
                    <select name="jenis_soal" class="form-control" onchange="togglePilihan(this)" required>
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
            <textarea name="pertanyaan" class="form-control" required>${pertanyaan}</textarea>
        </div>
        
        <div class="form-group mb-2 pilihan-container">
            <label>Pilihan Jawaban</label>
            <div id="pilihanList">${pilihanList}</div>
            <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="addOpsi()">Tambah Opsi</button>
        </div>
        <div class="form-group mb-2" id="jawabanContainer" style="${jawabanVisible ? '' : 'display: none;'}">
            <label>Jawaban</label>
            <input type="text" name="jawaban" class="form-control" value="${jawaban}">
        </div>
        <div class="form-group mb-2">
            <label>Bobot Total</label>
            <input type="number" name="bobot_total" id="bobot_total" class="form-control" value="${bobot}" readonly>
        </div>
        <div class="text-end">
            <button type="button" class="btn btn-light-dark" onclick="${isEdit ? `cancelEdit('${soal.id}')` : 'renderQuestions()'}">Batal</button>
            <button type="submit" class="btn btn-primary">${isEdit ? 'Update' : 'Simpan'}</button>
        </div>
    </div>
</form>`;

        return card;
    }

    function addQuestion() {
        const form = renderFormSoal('create');
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
                    if (kiri && kanan) {
                        pilihan.push({
                            teks: kiri,
                            pasangan: kanan
                        });
                    }
                });
            } else if (['pg', 'mpg', 'benar_salah'].includes(jenis)) {
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
        $.get(`<?= base_url('panel/banksoal/soal/detail/') ?>${id}`, function(res) {
            if (!res.status) return alert(res.message);

            const form = renderFormSoal('edit', res.data);
            $(`[data-id='${id}']`).replaceWith(form);

            setTimeout(() => {
                togglePilihan($('#editForm select[name="jenis_soal"]'), true);
            }, 20);

            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const jenis = form.find('select[name="jenis_soal"]').val();
                const data = form.serializeArray();
                const pilihan = [];

                if (jenis === 'jodohkan') {
                    form.find('#pilihanList .opsi-jodohkan').each(function() {
                        const kiri = $(this).find('.pilihan-kiri').val();
                        const kanan = $(this).find('.pilihan-kanan').val();
                        if (kiri && kanan) pilihan.push({
                            teks: kiri,
                            pasangan: kanan
                        });
                    });
                } else if (['pg', 'mpg', 'benar_salah'].includes(jenis)) {
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
                }

                if (jenis === 'esai' || jenis === 'isian') {
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
            jawabanContainer.show();
        }
    }


    function addOpsi(btn = null) {
        const form = btn ? $(btn).closest('form') : $('#questionForm').length ? $('#questionForm') : $('#editForm');
        const jenis = form.find('select[name="jenis_soal"]').val();
        const pilihanList = form.find('#pilihanList');

        pilihanList.append(renderOpsi('', jenis));
        hitungBobotTotal(); // panggil setelah nambah opsi
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
            total += val;
        });
        $('#bobot_total').val(total);
    }

    // Jalankan saat form dibuka
    $(document).on('input', '.opsi-bobot', function() {
        hitungBobotTotal();
    });


    $(document).ready(function() {
        renderQuestions();
    });
</script>
<?= $this->endSection(); ?>