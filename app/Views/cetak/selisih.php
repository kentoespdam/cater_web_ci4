<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="<?= base_url('/assets/js/tailwind.js') ?>"></script>
</head>

<body>
    <div class="flex flex-col w-full">
        <div class="flex flex-row justify-center items-center">
            <div>
                <img src="<?= base_url('/assets/images/logo_pdam.png') ?>" width="120" />
            </div>
            <div>
                <h3 class="text-3xl font-bold underline">
                    DATA Selisih Foto
                </h3>
            </div>
        </div>

        <div class="mt-8 flex flex-row">
            <table border="1" class="w-[300px] table">
                <tr>
                    <td class="w-2/6">Jumlah Data</td>
                    <td>: <?= $data->propertygrid->rows[0]->value ?></td>
                </tr>
                <tr>
                    <td>Jumlah Foto</td>
                    <td>: <?= $data->propertygrid->rows[1]->value ?></td>
                </tr>
                <tr>
                    <td>Selisih</td>
                    <td>: <?= $data->propertygrid->rows[2]->value ?></td>
                </tr>
            </table>
        </div>
        <hr />

        <div class="mt-8 flex flex-1">
            <table class="w-full table border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-3 text-left font-medium">Cabang</th>
                        <th class="px-4 py-3 text-left font-medium">Petugas</th>
                        <th class="px-4 py-3 text-left font-medium">No Pelanggan</th>
                        <th class="px-4 py-3 text-left font-medium">Nama</th>
                        <th class="px-4 py-3 text-left font-medium">Kampung</th>
                        <th class="px-4 py-3 text-left font-medium">Alamat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data->rows as $row) : ?>
                        <tr>
                            <td><?= $row->cabang ?></td>
                            <td><?= $row->petugas ?></td>
                            <td><?= $row->nosamw ?></td>
                            <td><?= $row->nama ?></td>
                            <td><?= $row->kampung ?></td>
                            <td><?= $row->alamat ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>