<?php
$this->extend("template/index");
$curYear = date('Y');
$curMonth = date('m');
?>

<?= $this->section('content') ?>
<div class="panel panel-default" style="height: 100%;">
    <div class="panel-content" style="height: 100%;">
        <div class="table-responsive" style="height: 100%;">
            <table border="1" id="dg" toolbar="#toolbar"></table>

            <div id="toolbar" class="easyui-toolbar">
                <div>
                    <input name="nosamw" id="nosamw" />
                </div>
                <div>
                    <select id="cabang">
                        <option value="" selected>-- Pilih Cabang --</option>
                        <?php foreach ($cabangs as $cabang) : ?>
                            <option value="<?= $cabang->id_cabang ?>"><?= $cabang->nm_cabang ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <select id="petugas"></select>
                </div>
                <div>
                    <select id="kampung"></select>
                </div>
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
                    <select name="kondisi" id="kondisi">
                        <option value="" selected>-- Pilih Kondisi --</option>
                        <?php foreach ($kondisis as $kondisi) : ?>
                            <option value="<?= $kondisi->kondisi ?>"><?= $kondisi->kondisi ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <select id="cek" name="cek">
                        <option value="0">Input Pembaca</option>
                        <option value="2">Success Koperasi</option>
                        <option value="1">Success Cabang</option>
                        <option value="3">Gagal</option>
                        <option value="4">ALL</option>
                    </select>
                </div>
                <div>
                    <a id="search" href="#">search</a>
                </div>
                <div>
                    <a id="reset" href="#">reset</a>
                </div>
                <div>
                    <a id="excel" href="#">print</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets') ?>/js/laporan/hasil_baca.js?<?= time() ?>" defer></script>
<?= $this->endSection() ?>