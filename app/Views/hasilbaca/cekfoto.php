<?php
$this->extend("template/index");
$curYear = date('Y');
$curMonth = date('m');
?>

<?= $this->section('content') ?>
<style>
    .detail-foto tr td {
        padding: 1em .5em .5em 1em;
        border: none;
    }
</style>
<div class="panel panel-default">
    <div class="panel-content">
        <div class="table-responsive">
            <table border="1" id="dg" toolbar="#toolbar" height="700"></table>
            <div id="toolbar" class="easyui-toolbar">
                <div>
                    <input id="nosamw" />
                </div>
                <div>
                    <input id="tglAwal">
                </div>
                <div>
                    <input id="tglAkhir">
                </div>
                <div>
                    <a id="search" href="#">search</a>
                </div>
                <div>
                    <a id="reset" href="#">reset</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets') ?>/js/bacameter/cekfoto.js" defer></script>
<?= $this->endSection() ?>