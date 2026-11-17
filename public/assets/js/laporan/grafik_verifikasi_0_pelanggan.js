$(function () {
    var root = $('#verification-customers'), body = $('#customer-rows');
    var request, satkerRequest, page = 1, totalPages = 1, applied;
    var columns = [];
    var query = new URLSearchParams(window.location.search);
    if (query.get('satker')) $('#customer-satker').append($('<option>', { value: query.get('satker') }).text(query.get('satker')));
    ['tahun', 'bulan', 'satker', 'jenis', 'status_manual', 'status_id', 'rows'].forEach(function (key) {
        if (query.has(key)) $('#customer-filters [name="' + key + '"]').val(query.get(key));
    });
    if (!query.has('bulan')) $('#customer-month').val(new Date().getMonth() + 1);

    function loadSatkers() {
        if (satkerRequest) satkerRequest.abort();
        var select = $('#customer-satker'), selected = select.val();
        $('#customer-satker-status').text('Memuat Satker…');
        satkerRequest = $.getJSON(root.attr('data-summary-endpoint'), { tahun: $('#customer-year').val() })
            .done(function (result) {
                if (!result || !Array.isArray(result.cabang)) {
                    $('#customer-satker-status').text('Daftar Satker belum tersedia.');
                    return;
                }
                select.empty().append($('<option>', { value: '' }).text('Semua Satker'));
                result.cabang.forEach(function (branch) { select.append($('<option>', { value: branch.id }).text(branch.nama)); });
                select.val(selected);
                if (select.val() === null) select.val('');
                $('#customer-satker-status').text('');
            }).fail(function (xhr, reason) {
                if (reason !== 'abort') $('#customer-satker-status').text('Daftar Satker belum tersedia. Muat ulang untuk mencoba lagi.');
            });
    }

    function changeKind() {
        var target = $('#customer-kind').val() === 'target';
        $('.customer-target-filter').toggle(target).find('select').prop('disabled', !target);
    }

    function state(message) {
        body.empty().append($('<tr>').append($('<td>', { colspan: columns.length + 1, class: 'verification-detail__state' }).text(message)));
    }

    function load() {
        if (request) request.abort();
        var target = applied.jenis === 'target';
        columns = target
            ? [['no_sam', 'No. sambungan'], ['periode', 'Periode'], ['nama', 'Nama'], ['alamat', 'Alamat'], ['unit', 'Unit'], ['satker', 'Satker'], ['status_verifikasi_manual', 'Status manual'], ['status_verifikasi_id', 'Status ID']]
            : [['nosamw', 'No. sambungan'], ['periode', 'Periode'], ['nama', 'Nama'], ['alamat', 'Alamat'], ['unit', 'Unit'], ['satker', 'Satker'], ['tgl_verif', 'Tanggal verifikasi']];
        var header = $('<tr>').append($('<th>', { scope: 'col' }).text('No.'));
        columns.forEach(function (column) { header.append($('<th>', { scope: 'col' }).text(column[1])); });
        $('#customer-head').empty().append(header);
        $('#customer-description').text(target
            ? 'Status manual dan status ID menggunakan aturan berbeda. Daftar ini memuat baris target; satu pelanggan dapat muncul pada beberapa periode.'
            : 'Aktivitas mentah mencakup semua verifikasi pada bulan pilihan, termasuk aktivitas yang tidak cocok dengan target. Filter status hanya tersedia untuk target pelanggan.');
        $('#customer-status').text('Memuat data pelanggan…');
        $('#customer-scroll').attr('aria-busy', 'true');
        $('#customer-prev, #customer-next').prop('disabled', true);
        state('Memuat data pelanggan…');
        request = $.getJSON(root.attr('data-endpoint'), $.extend({}, applied, { page: page }))
            .done(function (result) {
                if (!result || !Array.isArray(result.rows) || !Number.isFinite(Number(result.total))) {
                    $('#customer-status').text('Format data tidak sesuai.');
                    state('Pilih Muat ulang untuk mencoba lagi.');
                    return;
                }
                page = Number(result.page) || 1;
                var size = Number(result.rowsPerPage) || Number(applied.rows);
                var total = Number(result.total);
                totalPages = Math.max(1, Math.ceil(total / size));
                $('#customer-page').text('Halaman ' + page + ' dari ' + totalPages);
                $('#customer-prev').prop('disabled', page <= 1);
                $('#customer-next').prop('disabled', page >= totalPages);
                $('#customer-status').text(total ? ((page - 1) * size + 1) + '–' + ((page - 1) * size + result.rows.length) + ' dari ' + total.toLocaleString('id-ID') + ' baris' : 'Tidak ada data sesuai filter.');
                if (!result.rows.length) { state('Tidak ada data sesuai filter. Ubah periode, Satker, atau status.'); return; }
                body.empty();
                result.rows.forEach(function (record, index) {
                    var row = $('<tr>').append($('<td>').text((page - 1) * size + index + 1));
                    columns.forEach(function (column) { row.append($('<td>').text(record[column[0]] == null ? '—' : String(record[column[0]]))); });
                    body.append(row);
                });
            }).fail(function (xhr, reason) {
                if (reason === 'abort') return;
                $('#customer-status').text('Gagal memuat data pelanggan.');
                state('Periksa filter dan koneksi, lalu pilih Muat ulang.');
            }).always(function () { $('#customer-scroll').attr('aria-busy', 'false'); });
    }

    function applyFilters() {
        applied = {};
        $('#customer-filters').serializeArray().forEach(function (field) { if (field.value !== '') applied[field.name] = field.value; });
        page = 1;
        load();
    }
    $('#customer-kind').on('change', changeKind);
    $('#customer-filters').on('submit', function (event) { event.preventDefault(); applyFilters(); });
    $('#customer-prev').on('click', function () { if (page > 1) { page--; load(); } });
    $('#customer-next').on('click', function () { if (page < totalPages) { page++; load(); } });
    $('#customer-year').on('change', loadSatkers);
    $('#customer-retry').on('click', function () { load(); loadSatkers(); });
    changeKind();
    loadSatkers();
    applyFilters();
});
