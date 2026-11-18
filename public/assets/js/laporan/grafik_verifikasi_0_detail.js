$(function () {
    var page = $('#verification-detail');
    var rows = $('#detail-rows');
    var totals = $('#detail-totals');
    var status = $('#detail-status');
    var branches = [];
    var request;
    var loaded = false;
    var number = new Intl.NumberFormat('id-ID');
    var fields = ['target_awal_bulan', 'target_bulan_ini', 'aktivitas_verifikasi_mentah', 'akumulasi_mentah'];
    var labels = ['Target bruto', 'Target tersisa (Nomor Sambung cocok s.d. bulan lalu)', 'Verifikasi bulan ini (semua aktivitas)', 'Akumulasi verifikasi (semua aktivitas)'];
    var first = $('<tr>')
        .append($('<th>', { rowspan: 2, scope: 'col', class: 'verification-detail__year' }).text('Tahun'))
        .append($('<th>', { rowspan: 2, scope: 'col', class: 'verification-detail__month-label' }).text('Bulan'));
    var second = $('<tr>');
    var monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $('#detail-head').empty().append(first, second);
    $('#detail-notes').text('Target bulan ini = target bruto dikurangi pelanggan dengan No sambung yang cocok pada data verifikasi sampai bulan lalu. Verifikasi bulan ini dan akumulasi verifikasi menghitung seluruh aktivitas, termasuk yang tidak cocok dengan target.');

    function showState(message) {
        rows.empty().append($('<tr>').append($('<td>', {
            colspan: 2 + Math.max(branches.length, 1) * fields.length,
            class: 'verification-detail__state'
        }).text(message)));
        totals.empty();
    }

    function renderRows() {
        if (!loaded) return;
        if (branches.some(function (branch) { return !Array.isArray(branch.target_awal_bulan) || !Array.isArray(branch.target_bulan_ini) || !Array.isArray(branch.aktivitas_verifikasi_mentah); })) {
            status.text('Metrik laporan belum tersedia dalam snapshot ini.');
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
        var first = $('<tr>')
            .append($('<th>', { rowspan: 2, scope: 'col', class: 'verification-detail__year' }).text('Tahun'))
            .append($('<th>', { rowspan: 2, scope: 'col', class: 'verification-detail__month-label' }).text('Bulan'));
        var second = $('<tr>');
        visible.forEach(function (branch, branchIndex) {
            first.append($('<th>', {
                colspan: fields.length,
                scope: 'colgroup',
                class: 'verification-detail__branch-heading' + (branchIndex % 2 ? ' verification-detail__branch-heading--alternate' : '')
            }).text(branch.nama));
            labels.forEach(function (label, metricIndex) {
                var metricClass = 'verification-detail__metric';
                if (metricIndex === 0) metricClass += ' verification-detail__branch-start';
                if (metricIndex === fields.length - 1) metricClass += ' verification-detail__branch-end';
                second.append($('<th>', { scope: 'col', class: metricClass }).text(label));
            });
        });
        $('#detail-head').empty().append(first, second);
        rows.empty();
        totals.empty();
        var sums = new Array(visible.length * fields.length).fill(0);
        for (var month = 0; month < 12; month++) {
            var row = $('<tr>')
                .append($('<th>', { scope: 'row', class: 'verification-detail__year' }).text($('#detail-tahun').val()))
                .append($('<th>', { scope: 'row', class: 'verification-detail__month-label' }).text(monthNames[month]));
            visible.forEach(function (branch, branchIndex) {
                var raw = Number((branch.aktivitas_verifikasi_mentah || [])[month]) || 0;
                var previous = (branch.aktivitas_verifikasi_mentah || []).slice(0, month).reduce(function (sum, value) { return sum + (Number(value) || 0); }, 0);
                fields.forEach(function (field, index) {
                    var value = Number((branch[field] || [])[month]) || 0;
                    if (field === 'realisasi_bulan_ini') value = Number((branch.realisasi_bulan_ini || branch.verifikasi_bulan_ini || [])[month]) || 0;
                    if (field === 'akumulasi_mentah') value = previous + raw;
                    sums[branchIndex * fields.length + index] += value;
                    var cellClass = index === 0 ? 'verification-detail__branch-start' : '';
                    if (index === fields.length - 1) cellClass += ' verification-detail__branch-end';
                    row.append($('<td>', { class: cellClass.trim() }).text(number.format(value)));
                });
            });
            rows.append(row);
        }
        var total = $('<tr>').append($('<th>', { scope: 'row', class: 'verification-detail__year' }).text('Total')).append($('<th>', { class: 'verification-detail__month-label' }).text('12 bulan'));
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

    $('#detail-tahun').on('change', loadData);
    $('#detail-cabang').on('input', renderRows);
    $('#detail-refresh').on('click', loadData);
    loadData();
});
