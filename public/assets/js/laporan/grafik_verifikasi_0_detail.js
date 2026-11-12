$(function () {
    var page = $('#verification-detail');
    var rows = $('#detail-rows');
    var totals = $('#detail-totals');
    var status = $('#detail-status');
    var branches = [];
    var request;
    var loaded = false;
    var number = new Intl.NumberFormat('id-ID');
    var fields = ['target_bulan_ini', 'verifikasi_kumulatif', 'verifikasi_bulan_ini'];

    function showState(message) {
        rows.empty().append($('<tr>').append($('<td>', {
            colspan: 37,
            class: 'verification-detail__state'
        }).text(message)));
        totals.empty();
    }

    function renderRows() {
        if (!loaded) return;
        var query = $('#detail-cabang').val().trim().toLocaleLowerCase('id-ID');
        var visible = branches.filter(function (branch) {
            return String(branch.nama || '').toLocaleLowerCase('id-ID').includes(query);
        });
        status.text(visible.length + ' dari ' + branches.length + ' cabang · Tahun ' + $('#detail-tahun').val());
        if (!visible.length) {
            showState(branches.length ? 'Tidak ada cabang yang cocok dengan pencarian.' : 'Belum ada data verifikasi untuk tahun ini.');
            return;
        }
        rows.empty();
        totals.empty();
        var sums = new Array(36).fill(0);
        visible.forEach(function (branch) {
            var row = $('<tr>').append($('<th>', { scope: 'row', class: 'verification-detail__branch' }).text(branch.nama));
            for (var month = 0; month < 12; month++) {
                fields.forEach(function (field, index) {
                    var value = Number((branch[field] || [])[month]) || 0;
                    sums[month * 3 + index] += value;
                    row.append($('<td>').text(number.format(value)));
                });
            }
            rows.append(row);
        });
        var total = $('<tr>').append($('<th>', { scope: 'row', class: 'verification-detail__branch' }).text('Total cabang ditampilkan'));
        sums.forEach(function (value) { total.append($('<td>').text(number.format(value))); });
        totals.append(total);
    }

    function loadData() {
        if (request) request.abort();
        loaded = false;
        $('#detail-scroll').attr('aria-busy', 'true');
        status.text('Memuat data verifikasi…');
        showState('Memuat data verifikasi…');
        request = $.getJSON(page.attr('data-endpoint'), { tahun: $('#detail-tahun').val() })
            .done(function (result) {
                if (!result || !Array.isArray(result.cabang)) {
                    status.text('Data belum dapat ditampilkan.');
                    showState('Format data tidak sesuai. Silakan muat ulang.');
                    return;
                }
                branches = result.cabang;
                loaded = true;
                renderRows();
            })
            .fail(function (xhr, reason) {
                if (reason === 'abort') return;
                status.text('Gagal memuat data verifikasi.');
                showState('Data tidak dapat dimuat. Periksa koneksi lalu pilih Muat ulang.');
            })
            .always(function () { $('#detail-scroll').attr('aria-busy', 'false'); });
    }

    $('#detail-tahun').on('change', loadData);
    $('#detail-cabang').on('input', renderRows);
    $('#detail-refresh').on('click', loadData);
    loadData();
});
