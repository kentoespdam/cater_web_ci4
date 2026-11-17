<?php

declare(strict_types=1);

$this->extend('template/index');
?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= esc(base_url('assets/css/grafik_verifikasi_0_detail.css'), 'attr') ?>">
<div class="verification-detail" id="verification-detail" data-endpoint="<?= esc(base_url('api/laporan/grafik_verifikasi_0'), 'attr') ?>">
    <div class="verification-detail__heading">
        <div>
            <p class="verification-detail__eyebrow">Laporan / Verifikasi pembacaan</p>
            <h1>Detail verifikasi 0 meter</h1>
            <p class="verification-detail__description">Target dan hasil verifikasi setiap cabang, dari Januari hingga Desember.</p>
        </div>
        <a id="detail-pelanggan" class="btn btn-default" href="<?= esc(base_url('laporan/grafik_verifikasi_0/pelanggan'), 'attr') ?>">Daftar pelanggan</a>
        <a class="btn btn-default verification-detail__chart-link" href="<?= esc(base_url('laporan/grafik_verifikasi_0'), 'attr') ?>">
            <i class="fa fa-bar-chart" aria-hidden="true"></i> Lihat grafik
        </a>
    </div>

    <div class="verification-detail__panel">
        <div class="verification-detail__toolbar">
            <div class="verification-detail__field verification-detail__year">
                <label for="detail-tahun">Tahun laporan</label>
                <select id="detail-tahun" class="form-control">
                    <?php for ($year = (int) $tahun; $year >= (int) $tahun - 1; $year--) : ?>
                        <option value="<?= esc((string) $year, 'attr') ?>"><?= esc((string) $year) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="verification-detail__field verification-detail__search">
                <label for="detail-cabang">Cari cabang</label>
                <input type="search" id="detail-cabang" class="form-control" placeholder="Nama cabang…" autocomplete="off">
            </div>
            <div class="verification-detail__field"><label for="detail-perspektif">Perspektif</label><select id="detail-perspektif" class="form-control"><option value="manual">Workbook manual</option><option value="id">Rekonsiliasi ID pelanggan</option></select></div>
            <button type="button" id="detail-refresh" class="btn btn-default verification-detail__refresh">
                <i class="fa fa-refresh" aria-hidden="true"></i> Muat ulang
            </button>
        </div>

        <div class="verification-detail__table-info">
            <p id="detail-status" role="status" aria-live="polite">Memuat data verifikasi…</p>
            <span><i class="fa fa-arrows-h" aria-hidden="true"></i> Geser untuk melihat semua bulan</span>
        </div>
        <div class="verification-detail__scroll" tabindex="0" role="region" aria-label="Tabel verifikasi 0 meter per cabang, geser untuk melihat bulan lainnya" aria-busy="true" id="detail-scroll">
            <table class="verification-detail__table">
                <caption class="sr-only">Detail verifikasi 0 meter sesuai perspektif yang dipilih, per cabang dan bulan.</caption>
                <thead id="detail-head"></thead>
                <tbody id="detail-rows">
                    <tr><td colspan="37" class="verification-detail__state">Memuat data verifikasi…</td></tr>
                </tbody>
                <tfoot id="detail-totals"></tfoot>
            </table>
        </div>
        <div class="verification-detail__notes" id="detail-notes"></div>
    </div>
</div>
<script src="<?= esc(base_url('assets/js/laporan/grafik_verifikasi_0_detail.js'), 'attr') ?>" defer></script>
<?= $this->endSection() ?>
