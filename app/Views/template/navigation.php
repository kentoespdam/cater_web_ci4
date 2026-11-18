<?php

declare(strict_types=1);

$currentPath = trim(uri_string(), '/');
$navigationGroups = [
    ['label' => 'Master data', 'icon' => 'fa-database', 'prefix' => 'master', 'items' => $session['kdStatus'] == 'admin' ? [
        ['master/user', 'User', 'fa-user'],
        ['master/transfer_kampung', 'Transfer kampung', 'fa-right-left'],
    ] : []],
    ['label' => 'Cater PDAM', 'icon' => 'fa-droplet', 'prefix' => 'hasilbaca', 'items' => [
        ['', 'Hasil baca meter', 'fa-gauge'],
        ['hasilbaca/cekfoto', 'Cek foto', 'fa-camera'],
        ['hasilbaca/selisih', 'Selisih foto', 'fa-images'],
    ]],
    ['label' => 'Laporan', 'icon' => 'fa-chart-column', 'prefix' => 'laporan', 'items' => [
        ['laporan/hasil_baca', 'Hasil baca meter', 'fa-list'],
        ['laporan/target', 'Rekap target cater', 'fa-bullseye'],
        ['laporan/kondisi', 'Rekap per kondisi', 'fa-clipboard-list'],
        ['laporan/hasil_baca_0', 'Pemakaian 0', 'fa-droplet'],
        ['laporan/kondisi_pakai_0', 'Rekap pemakaian 0', 'fa-table-list'],
        ['laporan/grafik_verifikasi_0', 'Grafik verifikasi 0', 'fa-chart-column'],
        ['laporan/grafik_verifikasi_0/detail', 'Detail verifikasi 0', 'fa-table'],
    ]],
];
?>
<aside class="main-sidebar cater-navigation" aria-label="Navigasi utama">
    <section class="sidebar">
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= esc(base_url('assets/images/pdam2.png'), 'attr') ?>" width="40" height="40" alt="PDAM">
            </div>
            <div class="pull-left info">
                <p><?= esc($session['nama']) ?></p>
                <span class="cater-navigation__status"><i class="fa fa-circle" aria-hidden="true"></i> Online</span>
            </div>
        </div>
        <ul class="sidebar-menu">
            <li class="header">Navigasi utama</li>
            <?php foreach ($navigationGroups as $group) : ?>
                <?php $groupActive = str_starts_with($currentPath, $group['prefix'] . '/') || ($group['prefix'] === 'hasilbaca' && $currentPath === ''); ?>
                <li class="treeview<?= $groupActive ? ' active' : '' ?>">
                    <a href="#" role="button">
                        <i class="fa <?= esc($group['icon'], 'attr') ?>" aria-hidden="true"></i>
                        <span><?= esc($group['label']) ?></span>
                        <span class="pull-right-container"><i class="fa fa-angle-left pull-right" aria-hidden="true"></i></span>
                    </a>
                    <ul class="treeview-menu">
                        <?php foreach ($group['items'] as [$path, $label, $icon]) : ?>
                            <?php $itemActive = $currentPath === $path; ?>
                            <li<?= $itemActive ? ' class="active"' : '' ?>>
                                <a href="<?= esc(base_url($path), 'attr') ?>"<?= $itemActive ? ' aria-current="page"' : '' ?>>
                                    <i class="fa <?= esc($icon, 'attr') ?>" aria-hidden="true"></i><span><?= esc($label) ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
            <li class="cater-navigation__logout">
                <a href="<?= esc(base_url('auth/logout'), 'attr') ?>"><i class="fa fa-arrow-right-from-bracket" aria-hidden="true"></i> <span>Keluar</span></a>
            </li>
        </ul>
    </section>
</aside>
