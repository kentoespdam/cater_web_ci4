$(function () {
    var bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    var warna = {
        target: '#f77474',
        kumulatifSebelumnya: '#f2fa88',
        realisasi: '#80ffb5'
    };

    function loadData() {
        $.getJSON('/api/laporan/grafik_verifikasi_0', { tahun: $('#tahun').val() }, function (result) {
            var container = $('#grafikCabang').empty();
            result.cabang.forEach(function (cabang, index) {
                var canvasId = 'grafikVerifikasiCabang' + index;
                var panel = $('<div class="box verifikasi-chart"><div class="box-header"><h2 class="verifikasi-chart__title"></h2></div><div class="box-body verifikasi-chart__body"></div></div>');
                panel.find('.verifikasi-chart__title').text(cabang.nama);
                var chartBody = panel.find('.verifikasi-chart__body');
                chartBody.append($('<div class="verifikasi-chart__plot"></div>').append($('<canvas>').attr({
                    id: canvasId,
                    role: 'img',
                    'aria-label': 'Grafik verifikasi 0 meter ' + cabang.nama
                })));
                var legend = $('<ul class="verifikasi-chart__legend" aria-label="Keterangan warna grafik"></ul>');
                [
                    { color: warna.target, label: 'Target verifikasi' },
                    { color: warna.kumulatifSebelumnya, label: 'Verifikasi kumulatif sebelumnya' },
                    { color: warna.realisasi, label: 'Realisasi verifikasi bulan ini' }
                ].forEach(function (item) {
                    var legendItem = $('<li><span class="verifikasi-chart__swatch"></span><span></span></li>');
                    legendItem.find('.verifikasi-chart__swatch').css('background-color', item.color);
                    legendItem.find('span:last').text(item.label);
                    legend.append(legendItem);
                });
                panel.find('.box-header').append(legend);
                container.append(panel);

                var labelsReady = false;
                var chart = new Chart(document.getElementById(canvasId).getContext('2d')).Bar({
                    labels: bulan,
                    datasets: [{
                        label: 'Target verifikasi',
                        fillColor: warna.target,
                        data: cabang.target_bulan_ini
                    }, {
                        label: 'Verifikasi kumulatif',
                        fillColor: warna.realisasi,
                        data: cabang.verifikasi_kumulatif
                    }]
                }, {
                    responsive: true,
                    maintainAspectRatio: false,
                    barShowStroke: false,
                    scaleShowVerticalLines: false,
                    multiTooltipTemplate: function (bar) {
                        if (bar.datasetLabel === 'Target verifikasi') {
                            return 'Target verifikasi: ' + bar.value;
                        }

                        var bulanIndex = bulan.indexOf(bar.label);
                        var bulanIni = cabang.verifikasi_bulan_ini[bulanIndex] || 0;
                        return 'Verifikasi kumulatif: ' + bar.value + ' | Realisasi verifikasi bulan ini: ' + bulanIni;
                    },
                    onAnimationComplete: function () {
                        labelsReady = true;
                        drawChartLabels(this);
                    }
                });
                var drawChart = chart.draw;
                chart.draw = function () {
                    drawChart.apply(this, arguments);
                    if (labelsReady) {
                        drawChartLabels(this);
                    }
                };

                function drawChartLabels(chart) {
                    var context = chart.chart.ctx;

                    context.save();
                    context.fillStyle = '#263746';
                    context.font = '11px Arial';
                    context.textAlign = 'center';
                    context.textBaseline = 'bottom';
                    chart.datasets[0].bars.forEach(function (bar) {
                        context.fillText(bar.value, bar.x, Math.max(12, bar.y - 4));
                    });

                    var stackedBars = chart.datasets[1].bars;
                    context.textBaseline = 'middle';
                    stackedBars.forEach(function (bar, barIndex) {
                        var bulanIni = cabang.verifikasi_bulan_ini[barIndex] || 0;
                        var sebelumnya = Math.max(0, bar.value - bulanIni);
                        var ySebelumnya = chart.scale.calculateY(sebelumnya);
                        var tinggiSebelumnya = bar.base - ySebelumnya;
                        var tinggiBulanIni = ySebelumnya - bar.y;

                        context.fillStyle = warna.kumulatifSebelumnya;
                        context.fillRect(bar.x - bar.width / 2, ySebelumnya, bar.width, tinggiSebelumnya);

                        if (tinggiSebelumnya >= 12) {
                            context.fillStyle = '#263746';
                            context.fillText(sebelumnya, bar.x, ySebelumnya + tinggiSebelumnya / 2);
                        }
                        if (tinggiBulanIni >= 12) {
                            context.fillStyle = '#263746';
                            context.fillText(bulanIni, bar.x, bar.y + tinggiBulanIni / 2);
                        }
                    });

                    context.fillStyle = '#263746';
                    context.font = '600 11px Arial';
                    context.textBaseline = 'bottom';
                    stackedBars.forEach(function (bar) {
                        context.fillText(bar.value, bar.x, Math.max(12, bar.y - 4));
                    });
                    context.restore();
                }
            });
        });
    }

    $('#tahun').on('change', loadData);
    loadData();
});
