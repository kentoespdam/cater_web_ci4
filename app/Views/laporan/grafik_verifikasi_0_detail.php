<?php

declare(strict_types=1);

$this->extend('template/index');
$months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
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
                <caption class="sr-only">Detail verifikasi 0 meter: target, akumulasi verifikasi, dan verifikasi bulan ini per cabang dan bulan.</caption>
                <thead>
                    <tr>
                        <th rowspan="3" scope="col" class="verification-detail__branch">Cabang</th>
                        <?php foreach ($months as $month) : ?>
                            <th colspan="3" scope="colgroup" class="verification-detail__month"><?= esc($month) ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <?php foreach ($months as $month) : ?>
                            <th rowspan="2" scope="col" class="verification-detail__target">Target</th>
                            <th colspan="2" scope="colgroup" class="verification-detail__verification">Verifikasi</th>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <?php foreach ($months as $month) : ?>
                            <th scope="col" class="verification-detail__accumulated">Akumulasi</th>
                            <th scope="col" class="verification-detail__monthly">Bulan ini</th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody id="detail-rows">
                    <tr><td colspan="37" class="verification-detail__state">Memuat data verifikasi…</td></tr>
                </tbody>
                <tfoot id="detail-totals"></tfoot>
            </table>
        </div>
        <div class="verification-detail__notes">
            <span><strong>Target</strong> target verifikasi bulan berjalan.</span>
            <span><strong>Akumulasi</strong> total verifikasi hingga bulan tersebut.</span>
            <span><strong>Bulan ini</strong> realisasi verifikasi pada bulan tersebut.</span>
        </div>
    </div>
</div>
<script src="<?= esc(base_url('assets/js/laporan/grafik_verifikasi_0_detail.js'), 'attr') ?>" defer></script>
<?= $this->endSection() ?>
