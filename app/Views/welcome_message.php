<?php
$this->extend("template/index");
$curYear = date('Y');
$curMonth = date('m');
?>

<?= $this->section('content') ?>
<div class="panel panel-default">
    <div class="panel-content">
        <div class="table-responsive">
            <table border="1" id="dg" toolbar="#toolbar">
                <tfoot></tfoot>
            </table>

            <div id="toolbar" class="easyui-toolbar">
                <div>
                    <select id="tahun" name="tahun">
                        <?php for ($i = date('Y') - 1; $i <= date('Y'); $i++) : ?>
                            <option value="<?= $i ?>" <?= $i == $curYear ? "selected" : "" ?>><?= $i ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div>
                    <select id="bulan" name="bulan" class="easyui-combobox">
                        <?php foreach (listBulan() as $bln) : ?>
                            <option value="<?= $bln['code'] ?>" <?= $bln['code'] == $curMonth ? "selected" : "" ?>><?= $bln['value'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <a id="search" href="#">search</a>
                </div>
                <div>
                    <a id="reset" href="#">reset</a>
                </div>

                <div style="height: 30px; display:flex; align-items:center; gap: 10px; border: 1px solid #d0d0d0; padding: 0px 10px 0px 10px">
                    <span>Keterangan :</span>
                    <span style="color:red">< 60%</span>
                    <span>|</span>
                    <span style="color:blue">< 90</span>
                    <span>|</span>
                    <span style="color:green">> 90%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets') ?>/js/bacameter/script.js?<?= time() ?>" defer></script>
<?= $this->endSection() ?>