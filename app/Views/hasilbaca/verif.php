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

            <form action="#" id="toolbar" style="padding:.5em;" onsubmit="return false">
                <input name="nosamw" id="nosamw" required />
                <select id="cabang">
                    <option value="" selected>-- Pilih Cabang --</option>
                    <?php foreach ($cabangs as $cabang) : ?>
                        <option value="<?= $cabang->id_cabang ?>"><?= $cabang->nm_cabang ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="petugas"></select>
                <select id="kampung"></select>
                <select id="tahun" name="tahun">
                    <?php for ($i = date('Y') - 1; $i <= date('Y'); $i++) : ?>
                        <option value="<?= $i ?>" <?= $i == $curYear ? "selected" : "" ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
                <select id="bulan" name="bulan">
                    <?php foreach (listBulan() as $bln) : ?>
                        <option value="<?= $bln['code'] ?>" <?= $bln['code'] == $curMonth ? "selected" : "" ?>><?= $bln['value'] ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="cek" name="cek">
                    <option value="0">Input Pembaca</option>
                    <option value="2">Success Koperasi</option>
                    <option value="1">Success Cabang</option>
                    <option value="3">Gagal</option>
                </select>
                <a id="search" href="#">search</a>
                <a id="reset" href="#">reset</a>
        </div>
    </div>
</div>
</div>

<script src="<?= base_url('assets') ?>/js/bacameter/verif.js" defer></script>
<?= $this->endSection() ?>