<?php
$this->extend('template/index');
?>

<?= $this->section('content') ?>
<style>
    .verifikasi-chart { background: #fff; border-top: 2px solid #3c8dbc; }
    .verifikasi-chart__header { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 20px 24px; }
    .verifikasi-chart__title { margin: 0; color: #333; font-size: 20px; font-weight: 500; }
    .verifikasi-chart__description { margin: 6px 0 0; color: #777; }
    .verifikasi-chart .box-header:not(.verifikasi-chart__header) { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
    .verifikasi-chart__legend { display: flex; flex-wrap: wrap; gap: 8px 16px; margin: 0; padding: 0; list-style: none; }
    .verifikasi-chart__legend li { display: flex; align-items: center; gap: 8px; color: #555; font-size: 12px; }
    .verifikasi-chart__swatch { width: 12px; height: 12px; flex: 0 0 12px; border: 1px solid rgba(0, 0, 0, 0.18); border-radius: 2px; }
    .verifikasi-chart__filter { display: flex; align-items: center; gap: 8px; }
    .verifikasi-chart__filter label { margin: 0; font-weight: 400; color: #555; }
    .verifikasi-chart__filter select { min-width: 112px; }
    .verifikasi-chart__body { height: min(520px, 62vh); min-height: 320px; padding: 12px 24px 24px; overflow-x: auto; }
    .verifikasi-chart__plot { min-width: 720px; height: 100%; }
    .verifikasi-chart__plot canvas { display: block; }
    @media (max-width: 600px) {
        .verifikasi-chart__header { align-items: flex-start; flex-direction: column; padding: 16px; }
        .verifikasi-chart__body { height: 380px; padding: 8px 12px 16px; }
    }
</style>
<div class="box verifikasi-chart">
    <div class="box-header verifikasi-chart__header">
        <div>
            <h1 class="verifikasi-chart__title">Verifikasi pembacaan 0 meter</h1>
            <p class="verifikasi-chart__description">Angka di atas bar bertumpuk menunjukkan total verifikasi kumulatif</p>
        </div>
        <div class="verifikasi-chart__filter">
            <label for="tahun">Tahun</label>
            <select id="tahun" class="form-control" aria-label="Pilih tahun grafik">
            <?php for ($year = $tahun - 1; $year <= $tahun; $year++) : ?>
                <option value="<?= $year ?>" <?= $year === $tahun ? 'selected' : '' ?>><?= $year ?></option>
            <?php endfor; ?>
            </select>
        </div>
    </div>
</div>
<div id="grafikCabang"></div>
<script src="<?= base_url('assets') ?>/plugins/chartjs/Chart.min.js" defer></script>
<script src="<?= base_url('assets') ?>/js/laporan/grafik_verifikasi_0.js" defer></script>
<?= $this->endSection() ?>
