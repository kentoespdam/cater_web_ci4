<?php
$this->extend("template/index");
$curYear = date('Y');
$curMonth = date('m');
?>

<?= $this->section('content') ?>
<div class="panel panel-default" style="height: 100%;">
    <div class="panel-content" style="height: 100%;">
        <table id="pg">
            <thead>
                <tr>
                    <th>Jumlah Data</th>
                    <th>1</th>
                </tr>
                <tr>
                    <th>Jumlah Foto</th>
                    <th>1</th>
                </tr>
                <tr>
                    <th>Jumlah Selisih</th>
                    <th>1</th>
                </tr>
            </thead>
        </table>
        <div class="table-responsive" style="height: 100%;">
            <table border="1" id="dg" toolbar="#toolbar"></table>

            <div id="toolbar" class="easyui-toolbar">
                <div>
                    <select id="tahun" name="tahun">
                        <?php for ($i = date('Y') - 1; $i <= date('Y'); $i++) : ?>
                            <option value="<?= $i ?>" <?= $i == $curYear ? "selected" : "" ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <select id="bulan" name="bulan">
                        <?php foreach (listBulan() as $bln) : ?>
                            <option value="<?= $bln['code'] ?>" <?= $bln['code'] == $curMonth ? "selected" : "" ?>><?= $bln['value'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <input id="searchBy" />
                </div>
                <div>
                    <select id="cabang">
                        <option value="" selected>-- Pilih Cabang --</option>
                        <?php foreach ($cabangList as $cabang) : ?>
                            <option value="<?= $cabang->id_cabang ?>"><?= $cabang->nm_cabang ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <select id="petugas">
                        <option value="" selected>-- Pilih Petugas --</option>
                        <?php foreach ($pegawaiList as $pegawai) : ?>
                            <option value="<?= $pegawai->petugas ?>"><?= $pegawai->petugas ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <a id="search" href="#">search</a>
                </div>
                <div>
                    <a id="reset" href="#">reset</a>
                </div>
                <div>
                    <a id="cetak" href="#">print</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets') ?>/js/bacameter/selisih.js" defer></script>
<?= $this->endSection() ?>