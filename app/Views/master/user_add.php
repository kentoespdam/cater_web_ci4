<style>
    .easyui-form {
        padding-left: 10px;
        padding-right: 10px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .custom-layout {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding-top: 10px;
        padding-bottom: 10px;
    }
</style>
<div id="win" title="My Window">
    <form id="ff" method="post" class="easyui-layout custom-layout" data-options="fit:true">
        <!-- <div class="" > -->
        <div class="easyui-form">
            <div>
                <input id="user" name="user" width="100%" />
            </div>
            <div>
                <input id="nama" name="nama" />
            </div>
            <div>
                <input id="email" name="email" />
            </div>
            <div>
                <select id="cabang" name="cabang">
                    <option value="">--Pilih Cabang--</option>
                    <?php foreach ($cabangList as $cabang) : ?>
                        <option value="<?= $cabang->id_cabang ?>"><?= $cabang->nm_cabang ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div style="text-align:right; margin-right:10px;">
            <a href="#" id="submit">SIMPAN</a>
            <a href="#" id="reset">BATAL</a>
        </div>
    </form>
</div>

<div id="widow-footer">

</div>