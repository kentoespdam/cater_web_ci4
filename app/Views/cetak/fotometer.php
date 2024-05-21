<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="<?= base_url('/assets/js/tailwind.js') ?>"></script>
</head>

<body onload="window.print();
setTimeout(window.close, 0);">
    <div class="flex flex-col w-full">
        <div class="flex flex-row justify-center">
            <h3 class="text-3xl font-bold underline">
                DATA FOTO WM PELANGGAN
            </h3>
        </div>

        <div class="mt-8 flex flex-row justify-center">
            <table border="1">
                <tr>
                    <td rowspan="3" class="p-2">
                        <img src="<?= base_url('/assets/images/logo_pdam.png') ?>" width="120" />
                    </td>
                    <td class="pl-4 pr-4 font-bold">Nama</td>
                    <td>:</td>
                    <td><?= $data->nama ?></td>
                </tr>
                <tr>
                    <td class="pl-4 pr-4 font-bold">Alamat</td>
                    <td>:</td>
                    <td><?= $data->alamat ?></td>
                </tr>
                <tr>
                    <td class="pl-4 pr-4 font-bold">No Samb</td>
                    <td>:</td>
                    <td><?= $data->nosamw ?></td>
                </tr>
            </table>
        </div>

        <?php foreach ($data->rows as $row) : ?>
            <div class="mt-4 flex flex-row items-center">
                <div class="mr-8 ml-2">
                    <img src="data:image/jpg;base64, <?= $row->foto ?>" width="250" />
                </div>
                <div class="flex flex-col">
                    <table>
                        <tr>
                            <td class="pl-4 pr-4 font-bold">Bulan</td>
                            <td>:</td>
                            <td>
                                <?php
                                $exp = explode("-", $row->tgl);
                                echo $exp[0] . " - " . getMonthName((int)$exp[1])
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="pl-4 pr-4 font-bold">Tanggal Baca</td>
                            <td>:</td>
                            <td><?= $row->tgl_baca ?></td>
                        </tr>
                        <tr>
                            <td class="pl-4 pr-4 font-bold">Stan Kini</td>
                            <td>:</td>
                            <td><?= $row->stan_kini ?></td>
                        </tr>
                        <tr>
                            <td class="pl-4 pr-4 font-bold">Petugas</td>
                            <td>:</td>
                            <td><?= $row->petugas ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>