<?php
header("Content-Type: application/xls");
header("Content-Disposition: attachment; filename=$filename.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= $filename ?></title>
</head>

<body>
    <table border=1>
        <thead>
            <tr>
                <th align="center">tgl</th>
                <th align="center">no_sam</th>
                <th align="center">stan_kini</th>
                <th align="center">stan_lalu</th>
                <th align="center">pakai</th>
                <th align="center">petugas</th>
                <th align="center">kondisi</th>
                <th align="center">ket</th>
                <th align="center">info</th>
                <th align="center">ptgs_met</th>
                <th align="center">rata</th>
                <th align="center">kd_wil</th>
                <th align="center">wil</th>
                <th align="center">kd_cabang</th>
                <th align="center">nm_cabang</th>
                <th align="center">kd_cek</th>
                <th align="center">status_cek</th>
                <th align="center">nama</th>
                <th align="center">alamat</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row) : ?>
                <tr>
                    <td nowrap><?= $row->tgl ?></td>
                    <td nowrap>'<?= $row->no_sam ?></td>
                    <td nowrap><?= $row->stan_kini ?></td>
                    <td nowrap><?= $row->stan_lalu ?></td>
                    <td nowrap><?= $row->pakai ?></td>
                    <td nowrap><?= $row->petugas ?></td>
                    <td nowrap><?= $row->kondisi ?></td>
                    <td nowrap><?= $row->ket ?></td>
                    <td nowrap><?= $row->info ?></td>
                    <td nowrap>'<?= $row->ptgs_met ?></td>
                    <td nowrap><?= $row->rata ?></td>
                    <td nowrap>'<?= $row->kd_wil ?></td>
                    <td nowrap><?= $row->wil ?></td>
                    <td nowrap>'<?= $row->kd_cabang ?></td>
                    <td nowrap><?= $row->nm_cabang ?></td>
                    <td nowrap><?= $row->kd_cek ?></td>
                    <td nowrap><?= $row->status_cek ?></td>
                    <td nowrap><?= $row->nama ?></td>
                    <td nowrap><?= $row->alamat ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>