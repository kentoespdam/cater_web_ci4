$(function () {
    var months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    var container = $('#grafikCabang');
    var branches = [], charts = [], request;
    months.forEach(function (month, index) { $('#bulan').append($('<option>', { value: index }).text(month)); });
    $('#bulan').val(new Date().getMonth());

    function metrics(branch) {
        if ($('#perspektif').val() === 'manual') {
            return [branch.target_awal_bulan, branch.aktivitas_verifikasi_mentah];
        }
        return [branch.target_bulan_ini, branch.realisasi_bulan_ini || branch.verifikasi_bulan_ini, branch.verifikasi_kumulatif];
    }

    function draw(title, labels, values, names) {
        var panel = $('<section class="box verifikasi-chart"><div class="box-header"><h2 class="verifikasi-chart__title"></h2></div><div class="box-body verifikasi-chart__body"><div class="verifikasi-chart__plot"><canvas role="img"></canvas></div></div></section>');
        panel.find('h2').text(title);
        panel.find('canvas').attr('aria-label', title + '. ' + names.join(' dan ') + '. Angka lengkap tersedia pada Tabel detail.');
        var legend = $('<ul class="verifikasi-chart__legend"></ul>');
        var colors = ['#3c8dbc', '#66a897', '#bd9654'];
        names.forEach(function (name, index) {
            var item = $('<li><span class="verifikasi-chart__swatch"></span><span></span></li>');
            item.find('span:first').css('background-color', colors[index]);
            item.find('span:last').text(name);
            legend.append(item);
        });
        panel.find('.box-header').append(legend);
        container.append(panel);
        charts.push(new Chart(panel.find('canvas')[0].getContext('2d')).Bar({
            labels: labels,
            datasets: names.map(function (name, index) { return { label: name, fillColor: colors[index], data: values[index] }; })
        }, { responsive: true, maintainAspectRatio: false, animation: false, barShowStroke: false,
            scaleShowVerticalLines: false, multiTooltipTemplate: '<%= datasetLabel %>: <%= value %>' }));
    }

    function render() {
        charts.forEach(function (chart) { chart.destroy(); });
        charts = [];
        container.empty();
        var manual = $('#perspektif').val() === 'manual';
        var comparison = $('#tampilan').val() === 'cabang';
        $('#bulan-filter').toggle(comparison);
        $('#perspektif-info').text(manual
            ? 'Workbook manual: target awal (bruto) dibandingkan aktivitas verifikasi mentah, termasuk aktivitas yang tidak cocok dengan target bulan tersebut. Rumus belum verifikasi tersedia di tabel detail.'
            : 'Rekonsiliasi ID: target neto dibandingkan realisasi yang cocok dengan pelanggan target bulan tersebut. Sisa ID dan akumulasi tersedia di tabel detail.');
        if (!branches.length) return;
        if (manual && branches.some(function (branch) { return !Array.isArray(branch.target_belum_verifikasi_manual); })) {
            container.append($('<div>', { class: 'callout callout-warning' }).text('Metrik workbook manual belum tersedia dalam snapshot ini. Perbarui snapshot dengan: php spark laporan:generate-verif0-snapshot --tahun=' + $('#tahun').val()));
            return;
        }
        var names = manual ? ['Target awal (bruto)', 'Aktivitas verifikasi mentah'] : ['Target neto ID', 'Realisasi cocok bulan ini', 'Akumulasi cocok'];
        if (comparison) {
            var month = Number($('#bulan').val());
            draw('Perbandingan cabang · ' + months[month], branches.map(function (b) { return b.nama; }), names.map(function (name, i) {
                return branches.map(function (b) { return Number((metrics(b)[i] || [])[month]) || 0; });
            }), names);
        } else {
            branches.forEach(function (branch) { draw(branch.nama, months, metrics(branch), names); });
        }
    }

    function loadData() {
        if (request) request.abort();
        branches = [];
        render();
        container.attr('aria-busy', 'true');
        $('#snapshotInfo').empty();
        $('#grafik-status').text('Memuat data grafik…');
        request = $.getJSON(container.attr('data-endpoint'), { tahun: $('#tahun').val() })
            .done(function (result) {
                if (!result || !Array.isArray(result.cabang)) {
                    $('#grafik-status').text('Format data tidak sesuai. Pilih Muat ulang untuk mencoba lagi.');
                    return;
                }
                branches = result.cabang;
                $('#grafik-status').text(branches.length ? branches.length + ' cabang · Tahun ' + $('#tahun').val() : 'Snapshot untuk tahun ini belum tersedia.');
                if (result.updated_at) $('#snapshotInfo').text('Pembaruan snapshot: ' + result.updated_at);
                render();
            }).fail(function (xhr, reason) {
                if (reason !== 'abort') $('#grafik-status').text(xhr.status === 404 ? 'Snapshot untuk tahun ini belum tersedia.' : 'Data grafik tidak dapat dimuat. Pilih Muat ulang untuk mencoba lagi.');
            }).always(function () { container.attr('aria-busy', 'false'); });
    }
    $('#tahun').on('change', loadData);
    $('#grafik-refresh').on('click', loadData);
    $('#perspektif, #tampilan, #bulan').on('change', render);
    loadData();
});
