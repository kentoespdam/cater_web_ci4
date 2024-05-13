<?php
$this->extend("template/index");
$curYear = date('Y');
$curMonth = date('m');
?>

<?= $this->section('content') ?>
<div class="panel panel-default">
    <div class="panel-content">

        <div class="easyui-layout" style="width:700px;height:500px;">
            <div data-options="region:'center',iconCls:'icon-ok'" title="Transfer User">
                <div class="easyui-layout" data-options="fit:true">
                    <div region="west" split="true" style="width:50%; padding:5px;">
                        <div region="north" split="true" fit="true" style="padding:5px; height:80px;">
                            <select id="src_user" name="src_user">
                                <option>-- Pilih Sumber User --</option>
                                <?php foreach ($userList as $u) : ?>
                                    <option value="<?= $u->username ?>"><?= $u->username ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div region="center" split="true" style="padding:5px">
                            <?php foreach ($kampungList as $kampung) : ?>
                                <div style="margin-bottom: 5px;">
                                    <input id="src_kampung_<?= $kampung->id ?>" name="src_kampung[]">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div region="center" split="true" style="width:50%; padding:5px;">
                        <div region="north" split="true" fit="true" style="padding:5px; height:80px;">
                            <select id="dst_user" name="dst_user">
                                <option>-- Pilih Target User --</option>
                                <?php foreach ($userList as $u) : ?>
                                    <option value="<?= $u->username ?>"><?= $u->username ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div data-options="region:'south'">
                            <ul id="dst_kampung">
                            </ul>
                        </div>
                    </div>
                    <div region="south">
                        bottom
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const kampungList = JSON.parse(`<?= json_encode($kampungList) ?>`);
</script>
<script src="<?= base_url('assets') ?>/js/master/transfer_kampung.js" defer></script>

<?= $this->endSection() ?>