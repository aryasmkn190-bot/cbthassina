<?= $this->extend('Layout/main_peserta'); ?>

<?= $this->section('css'); ?>
<style>
    .timeline-item {
        position: relative;
        padding-left: 30px;
        border-left: 2px solid #e2e8f0;
        padding-bottom: 24px;
        transition: transform 0.2s ease, opacity 0.2s ease;
    }
    .timeline-item:last-child {
        border-left: 0;
        padding-bottom: 0;
    }
    .timeline-dot {
        position: absolute;
        left: -6px;
        top: 6px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #4f46e5;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.15);
    }
    .timeline-time {
        font-size: 0.75rem;
        font-weight: 700;
        color: #4f46e5;
        display: inline-block;
        padding: 2px 8px;
        background: rgba(79, 70, 229, 0.08);
        border-radius: 20px;
        margin-bottom: 6px;
    }
    .day-selector-btn {
        transition: all 0.2s ease;
        font-weight: 700;
        font-size: 0.85rem;
    }
    .day-selector-btn.active {
        background: #4f46e5 !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    }
    body.dark .timeline-item {
        border-left-color: #253b5c;
    }
    body.dark .timeline-dot {
        background: #8b5cf6;
    }
    body.dark .timeline-time {
        color: #a78bfa;
        background: rgba(139, 92, 246, 0.15);
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="layout-px-spacing animate-fade-in-up">
    <div class="middle-content container-xxl p-0">
        
        <!-- Desktop Header Welcome -->
        <div class="card mb-4 p-4 rounded-4 shadow-sm border-0 d-none d-md-flex flex-row justify-content-between align-items-center bg-white" style="border: 1px solid rgba(0,0,0,0.03) !important;">
            <div>
                <h4 class="mb-1 fw-bold text-dark">Jadwal Pelajaran Kelas</h4>
                <p class="mb-0 text-muted small">Kelas: <?= esc($peserta['kelas'] ?? '-') ?> | Tahun Ajaran: 2025/2026</p>
            </div>
            <div class="text-primary font-weight-bold">
                <i data-feather="calendar" style="width: 24px; height: 24px;"></i>
            </div>
        </div>

        <!-- Main Schedule Container -->
        <div class="card p-4 rounded-4 shadow-sm border-0 bg-white mb-4">
            <h5 class="fw-bold mb-3 text-dark d-md-none">Jadwal Kelas</h5>

            <!-- Day Selector Tabs -->
            <div class="d-flex justify-content-between mb-4 bg-light p-1.5 rounded-pill overflow-auto" style="gap: 4px; padding: 6px;">
                <button class="btn btn-sm btn-light rounded-pill flex-grow-1 py-2 day-selector-btn active" data-day="senin">Senin</button>
                <button class="btn btn-sm btn-light rounded-pill flex-grow-1 py-2 day-selector-btn" data-day="selasa">Selasa</button>
                <button class="btn btn-sm btn-light rounded-pill flex-grow-1 py-2 day-selector-btn" data-day="rabu">Rabu</button>
                <button class="btn btn-sm btn-light rounded-pill flex-grow-1 py-2 day-selector-btn" data-day="kamis">Kamis</button>
                <button class="btn btn-sm btn-light rounded-pill flex-grow-1 py-2 day-selector-btn" data-day="jumat">Jumat</button>
            </div>

            <!-- Schedule Timelines -->
            <div id="schedule-timeline-container" class="py-2">
                <!-- Timeline items will be injected or filtered -->
                <?php
                $hariList = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];
                foreach ($hariList as $h):
                    $filtered = array_filter($jadwal, function($j) use ($h) {
                        return strtolower($j['hari']) === $h;
                    });
                ?>
                    <div class="day-group <?= $h !== 'senin' ? 'd-none' : '' ?>" id="day-group-<?= $h ?>">
                        <?php if (empty($filtered)): ?>
                            <div class="text-center py-5 text-muted">
                                <i data-feather="slash" class="mb-2 text-muted" style="width: 32px; height: 32px;"></i>
                                <p class="small mb-0">Tidak ada jadwal pelajaran pada hari <?= ucfirst($h) ?>.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($filtered as $j): ?>
                                <div class="timeline-item">
                                    <div class="timeline-dot"></div>
                                    <span class="timeline-time">
                                        <i data-feather="clock" class="me-1" style="width: 10px; height: 10px;"></i>
                                        <?= substr($j['waktu_mulai'], 0, 5) ?> - <?= substr($j['waktu_selesai'], 0, 5) ?> WIB
                                    </span>
                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;"><?= esc($j['mata_pelajaran']) ?></h6>
                                    <p class="text-muted small mb-0 d-flex flex-wrap gap-3">
                                        <span><i data-feather="user" class="me-1" style="width: 12px; height: 12px;"></i> <?= esc($j['guru_nama']) ?></span>
                                        <span><i data-feather="map-pin" class="me-1" style="width: 12px; height: 12px;"></i> Ruangan: <?= esc($j['ruangan']) ?></span>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('pagejs'); ?>
<script>
    $(document).ready(function() {
        $(".day-selector-btn").on("click", function() {
            $(".day-selector-btn").removeClass("active");
            $(this).addClass("active");

            const selectedDay = $(this).data("day");
            
            // Hide all day groups with a quick fade animation
            $(".day-group").addClass("d-none");
            $(`#day-group-${selectedDay}`).removeClass("d-none").css("opacity", 0).animate({opacity: 1}, 250);
        });
    });
</script>
<?= $this->endSection(); ?>
