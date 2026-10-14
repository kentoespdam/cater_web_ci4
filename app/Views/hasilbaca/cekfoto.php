<?php
$this->extend("template/index");
$curYear = date('Y');
$curMonth = date('m');
?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('assets') ?>/libs/viewerjs/viewer.min.css">
<style>
    .detail-foto tr td {
        padding: 1em .5em .5em 1em;
        border: none;
    }
    .img-previewable {
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .img-previewable:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 10px rgba(0,0,0,0.25);
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
                <div>
                    <a id="cetak" href="#">print</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets') ?>/libs/viewerjs/viewer.min.js"></script>
<script src="<?= base_url('assets') ?>/js/bacameter/cekfoto.js?<?= time() ?>" defer></script>
<?= $this->endSection() ?>