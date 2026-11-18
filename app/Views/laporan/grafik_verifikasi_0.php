<?php
declare(strict_types=1);

$this->extend('template/index');
?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= esc(base_url('assets/css/verification-report.css'), 'attr') ?>">
<div class="verification-report">
    <div class="box verifikasi-chart">
        <div class="box-header verifikasi-chart__header">
            <div>
                <p class="verification-report__context">Laporan / Verifikasi pembacaan</p>
                <h1 class="verifikasi-chart__title">Verifikasi pembacaan 0 meter</h1>
                <p class="verifikasi-chart__description">
                    Tren target dan aktivitas verifikasi bulanan per cabang
                    <span id="snapshotInfo" class="text-muted"></span>
                </p>
            </div>
            <div class="verifikasi-chart__filter">
                <label for="tahun">Tahun</label>
                <select id="tahun" class="form-control" aria-label="Pilih tahun grafik">
                <?php for ($year = $tahun - 1; $year <= $tahun; $year++) : ?>
                    <option value="<?= $year ?>" <?= $year === $tahun ? 'selected' : '' ?>><?= $year ?></option>
                <?php endfor; ?>
                </select>
                <a class="btn btn-default" href="<?= esc(base_url('laporan/grafik_verifikasi_0/detail'), 'attr') ?>">
                    <i class="fa fa-table" aria-hidden="true"></i> Tabel detail
                </a>
            </div>
        </div>
        <div class="verifikasi-chart__controls">
            <button type="button" id="grafik-refresh" class="btn btn-default">Muat ulang</button>
            <p id="grafik-info" class="help-block"></p>
        </div>
    </div>
    <p id="grafik-status" role="status" aria-live="polite"></p>
    <div id="grafikCabang" data-endpoint="<?= esc(base_url('api/laporan/grafik_verifikasi_0'), 'attr') ?>"></div>
</div>
<script src="<?= esc(base_url('assets/plugins/chartjs/Chart.min.js'), 'attr') ?>" defer></script>
<script src="<?= esc(base_url('assets/js/laporan/grafik_verifikasi_0.js'), 'attr') ?>" defer></script>
<?= $this->endSection() ?>
