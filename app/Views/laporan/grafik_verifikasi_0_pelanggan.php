<?php

declare(strict_types=1);

$this->extend('template/index');
$months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
?>
<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= esc(base_url('assets/css/grafik_verifikasi_0_detail.css'), 'attr') ?>">
<link rel="stylesheet" href="<?= esc(base_url('assets/css/grafik_verifikasi_0_pelanggan.css'), 'attr') ?>">
<div class="verification-detail verification-customers" id="verification-customers" data-endpoint="<?= esc(base_url('api/laporan/grafik_verifikasi_0/pelanggan'), 'attr') ?>" data-summary-endpoint="<?= esc(base_url('api/laporan/grafik_verifikasi_0'), 'attr') ?>">
    <div class="verification-detail__heading">
        <div>
            <h1>Pelanggan verifikasi 0 meter</h1>
            <p class="verification-detail__description">Telusuri baris target atau aktivitas verifikasi mentah pada satu periode.</p>
        </div>
        <a class="btn btn-default" href="<?= esc(base_url('laporan/grafik_verifikasi_0/detail'), 'attr') ?>">Kembali ke tabel detail</a>
    </div>
    <div class="verification-detail__panel">
        <form id="customer-filters" class="verification-detail__toolbar">
            <div class="verification-detail__field"><label for="customer-year">Tahun</label><input id="customer-year" class="form-control" name="tahun" type="number" min="2000" max="2100" required value="<?= esc((string) $tahun, 'attr') ?>"></div>
            <div class="verification-detail__field"><label for="customer-month">Bulan</label><select id="customer-month" name="bulan" class="form-control">
                <?php foreach ($months as $index => $month) : ?>
                    <option value="<?= esc((string) ($index + 1), 'attr') ?>"><?= esc($month) ?></option>
                <?php endforeach; ?>
            </select></div>
            <div class="verification-detail__field"><label for="customer-kind">Jenis data</label><select id="customer-kind" name="jenis" class="form-control"><option value="target">Target pelanggan</option><option value="verifikasi">Aktivitas verifikasi mentah</option></select></div>
            <div class="verification-detail__field"><label for="customer-satker">Satker</label><select id="customer-satker" name="satker" class="form-control"><option value="">Semua Satker</option></select><small id="customer-satker-status" role="status"></small></div>
            <div class="verification-detail__field customer-target-filter"><label for="customer-manual">Status manual</label><select id="customer-manual" name="status_manual" class="form-control"><option value="">Semua status</option><option>Belum Diverifikasi</option><option>Sudah Diverifikasi</option></select></div>
            <div class="verification-detail__field customer-target-filter"><label for="customer-id">Status ID</label><select id="customer-id" name="status_id" class="form-control"><option value="">Semua status</option><option>Belum Diverifikasi</option><option>Sudah Diverifikasi</option></select></div>
            <div class="verification-detail__field"><label for="customer-size">Baris per halaman</label><select id="customer-size" name="rows" class="form-control"><option>25</option><option>50</option><option>100</option></select></div>
            <button type="submit" class="btn btn-default">Terapkan filter</button>
        </form>
        <p id="customer-description" class="verification-detail__notes"></p>
        <div class="verification-detail__table-info"><p id="customer-status" role="status" aria-live="polite">Memuat pelanggan…</p><span>Geser tabel untuk melihat semua kolom</span></div>
        <div id="customer-scroll" class="verification-detail__scroll" tabindex="0" role="region" aria-label="Daftar pelanggan verifikasi" aria-busy="true">
            <table class="verification-detail__table"><caption class="sr-only">Pelanggan sesuai periode dan filter yang diterapkan</caption><thead id="customer-head"></thead><tbody id="customer-rows"></tbody></table>
        </div>
        <nav class="customer-pagination" aria-label="Halaman pelanggan"><button id="customer-prev" type="button" class="btn btn-default" disabled>Sebelumnya</button><span id="customer-page">Halaman 1</span><button id="customer-next" type="button" class="btn btn-default" disabled>Berikutnya</button><button id="customer-retry" type="button" class="btn btn-default">Muat ulang</button></nav>
    </div>
</div>
<script src="<?= esc(base_url('assets/js/laporan/grafik_verifikasi_0_pelanggan.js'), 'attr') ?>" defer></script>
<?= $this->endSection() ?>
