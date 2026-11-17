$(function () {
    var months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    var container = $('#grafikCabang');
    var branches = [], charts = [], request;
    var number = new Intl.NumberFormat('id-ID');
    function metrics(branch) {
        var raw = branch.aktivitas_verifikasi_mentah || [];
        var cumulative = raw.map(function (value, index) {
            return raw.slice(0, index + 1).reduce(function (sum, month) { return sum + (Number(month) || 0); }, 0);
        });
        return [branch.target_awal_bulan, branch.target_bulan_ini, raw, cumulative];
    }

    function draw(title, labels, values, names) {
        var panel = $('<section class="box verifikasi-chart"><div class="box-header"><h2 class="verifikasi-chart__title"></h2></div><div class="box-body verifikasi-chart__body"><div class="verifikasi-chart__plot"><canvas role="img"></canvas></div></div></section>');
        panel.find('h2').text(title);
        panel.find('canvas').attr('aria-label', title + '. ' + names.join(' dan ') + '. Angka lengkap tersedia pada Tabel detail.');
        var legend = $('<ul class="verifikasi-chart__legend"></ul>');
        var colors = ['#3c8dbc', '#66a897', '#bd9654', '#76549a'];
        names.forEach(function (name, index) {
            var item = $('<li><span class="verifikasi-chart__swatch"></span><span></span></li>');
            item.find('span:first').css('background-color', colors[index]);
            item.find('span:last').text(name);
            legend.append(item);
        });
        panel.find('.box-header').append(legend);
        container.append(panel);
        var chart = new Chart(panel.find('canvas')[0].getContext('2d')).Bar({
            labels: labels,
            datasets: names.map(function (name, index) { return { label: name, fillColor: colors[index], data: values[index] }; })
        }, { responsive: true, maintainAspectRatio: false, animation: false, barShowStroke: false,
            scaleShowVerticalLines: false, multiTooltipTemplate: '<%= datasetLabel %>: <%= value %>' });
        var drawChart = chart.draw;
        chart.draw = function () {
            drawChart.apply(this, arguments);
            drawValueLabels(this);
        };
        drawValueLabels(chart);
        charts.push(chart);
    }

    function drawValueLabels(chart) {
        var context = chart.chart.ctx;
        context.save();
        context.fillStyle = '#263746';
        context.font = '600 11px Arial';
        context.textAlign = 'center';
        context.textBaseline = 'bottom';
        chart.datasets.forEach(function (dataset) {
            dataset.bars.forEach(function (bar) {
                context.fillText(number.format(Number(bar.value) || 0), bar.x, Math.max(12, bar.y - 5));
            });
        });
        context.restore();
    }

    function render() {
        charts.forEach(function (chart) { chart.destroy(); });
        charts = [];
        container.empty();
        $('#grafik-info').text('Target bulan ini = target bruto dikurangi pelanggan dengan ID yang cocok pada data verifikasi sampai bulan lalu. Verifikasi bulan ini dan akumulasi verifikasi menghitung seluruh aktivitas, termasuk yang tidak cocok dengan target.');
        if (!branches.length) return;
        if (branches.some(function (branch) { return !Array.isArray(branch.target_awal_bulan) || !Array.isArray(branch.target_bulan_ini) || !Array.isArray(branch.aktivitas_verifikasi_mentah); })) {
            container.append($('<div>', { class: 'callout callout-warning' }).text('Snapshot belum memuat semua metrik laporan. Perbarui snapshot dengan: php spark laporan:generate-verif0-snapshot --tahun=' + $('#tahun').val()));
            return;
        }
        var names = ['Target bruto', 'Target tersisa (ID cocok s.d. bulan lalu)', 'Verifikasi bulan ini (semua aktivitas)', 'Akumulasi verifikasi (semua aktivitas)'];
        branches.forEach(function (branch) { draw(branch.nama, months, metrics(branch), names); });
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
    loadData();
});
