$(function () {
    var page = $('#verification-detail');
    var rows = $('#detail-rows');
    var totals = $('#detail-totals');
    var status = $('#detail-status');
    var branches = [];
    var request;
    var loaded = false;
    var number = new Intl.NumberFormat('id-ID');
    var fields = [];
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    function perspective() {
        var manual = $('#detail-perspektif').val() === 'manual';
        fields = manual ? ['target_awal_bulan', 'target_belum_verifikasi_manual', 'sebelumnya', 'aktivitas_verifikasi_mentah', 'akumulasi_mentah', 'sisa_manual'] : ['target_bulan_ini', 'verifikasi_kumulatif', 'realisasi_bulan_ini', 'sisa_id'];
        var labels = manual ? ['Target awal (bruto)', 'Target belum verif', 'Aktivitas sebelumnya', 'Aktivitas bulan ini', 'Akumulasi aktivitas', 'Belum verif (rumus)'] : ['Target neto ID', 'Akumulasi cocok', 'Cocok bulan ini', 'Sisa ID'];
        var first = $('<tr>').append($('<th>', { rowspan: 2, scope: 'col', class: 'verification-detail__branch' }).text('Cabang'));
        var second = $('<tr>');
        months.forEach(function (month) {
            first.append($('<th>', { colspan: fields.length, scope: 'colgroup', class: 'verification-detail__month' }).text(month));
            labels.forEach(function (label) { second.append($('<th>', { scope: 'col', class: 'verification-detail__month' }).text(label)); });
        });
        $('#detail-head').empty().append(first, second);
        $('#detail-notes').text(manual ? 'Target belum verif = baris target yang belum pernah diverifikasi sebelumnya pada tahun laporan. Belum verif (rumus) = target belum verif − akumulasi aktivitas mentah sebelum bulan ini. Nilai negatif tetap ditampilkan sesuai workbook; ini bukan jumlah pelanggan tersisa.' : 'Target neto ID adalah target bulan berjalan. Akumulasi cocok memuat verifikasi ID yang cocok hingga bulan tersebut. Sisa ID = target neto ID − realisasi cocok bulan ini (minimum 0).');
        if (loaded) renderRows();
    }

    function showState(message) {
        rows.empty().append($('<tr>').append($('<td>', {
            colspan: 1 + 12 * fields.length,
            class: 'verification-detail__state'
        }).text(message)));
        totals.empty();
    }

    function renderRows() {
        if (!loaded) return;
        if ($('#detail-perspektif').val() === 'manual' && branches.some(function (branch) { return !Array.isArray(branch.target_belum_verifikasi_manual); })) {
            status.text('Metrik workbook manual belum tersedia dalam snapshot ini.');
            showState('Perbarui snapshot dengan: php spark laporan:generate-verif0-snapshot --tahun=' + $('#detail-tahun').val());
            return;
        }
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
        var sums = new Array(12 * fields.length).fill(0);
        visible.forEach(function (branch) {
            var row = $('<tr>').append($('<th>', { scope: 'row', class: 'verification-detail__branch' }).text(branch.nama));
            var previous = 0;
            for (var month = 0; month < 12; month++) {
                var raw = Number((branch.aktivitas_verifikasi_mentah || [])[month]) || 0;
                fields.forEach(function (field, index) {
                    var value = Number((branch[field] || [])[month]) || 0;
                    if (field === 'realisasi_bulan_ini') value = Number((branch.realisasi_bulan_ini || branch.verifikasi_bulan_ini || [])[month]) || 0;
                    if (field === 'sebelumnya') value = previous;
                    if (field === 'akumulasi_mentah') value = previous + raw;
                    if (field === 'sisa_manual') value = (Number((branch.target_belum_verifikasi_manual || [])[month]) || 0) - previous;
                    if (field === 'sisa_id') value = Math.max(0, (Number((branch.target_bulan_ini || [])[month]) || 0) - (Number((branch.realisasi_bulan_ini || branch.verifikasi_bulan_ini || [])[month]) || 0));
                    sums[month * fields.length + index] += value;
                    row.append($('<td>').text(number.format(value)));
                });
                previous += raw;
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
        var customerLink = new URL($('#detail-pelanggan').attr('href'), window.location.href);
        customerLink.searchParams.set('tahun', $('#detail-tahun').val());
        $('#detail-pelanggan').attr('href', customerLink.toString());
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

    $('#detail-perspektif').on('change', perspective);
    perspective();
    $('#detail-tahun').on('change', loadData);
    $('#detail-cabang').on('input', renderRows);
    $('#detail-refresh').on('click', loadData);
    loadData();
});
