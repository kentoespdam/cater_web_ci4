<?php
$this->extend("template/index");
$curYear = date('Y');
$curMonth = date('m');
?>

<?= $this->section('content') ?>
<div class="panel panel-default">
    <div class="panel-content">
        <div class="table-responsive">
            <table border="1" id="dg" width="100%" toolbar="#toolbar"></table>
            <div id="toolbar" class="easyui-toolbar">
                <a href="#" id="add">Add Pegawai</a>
            </div>
        </div>
    </div>
</div>

<?= $this->include('master/user_add') ?>
<script src="<?= base_url('assets') ?>/js/master/user.js?<?= strtotime("now") ?>" defer></script>
<script src="<?= base_url('assets') ?>/js/master/user_add.js" defer></script>

<?= $this->endSection() ?>