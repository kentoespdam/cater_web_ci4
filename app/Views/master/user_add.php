<style>
    .easyui-form {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
</style>
<div id="win" title="My Window" style="width:300px;height:100px;padding:5px; display:none;">
    <form id="ff" method="post" class="easyui-form">
        <div>
            <input id="user" width="100%" />
        </div>
        <div>
            <input id="nama" />
        </div>
        <div>
            <input id="cabang" />
        </div>
        <div>
            <a href="#" id="submit">SIMPAN</a>
            <a href="#" id="reset">BATAL</a>
        </div>
    </form>
</div>