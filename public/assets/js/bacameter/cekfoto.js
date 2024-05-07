const dg = $("#dg");
const nosamwTxt = $("#nosamw");
const tglAwalTxt = $("#tglAwal");
const tglAkhirTxt = $("#tglAkhir");
const searchBt = $("#search");
const resetBt = $("#reset");
const apiUri = `${baseUri}/api/cekfoto`;

nosamwTxt.textbox({
	width: "120px",
	prompt: "No. Pelanggan",
});

tglAwalTxt.datebox({
	width: "120px",
	prompt: "Tgl. Awal",
	formatter: dateFormatter,
	parser: dateParser,
});

tglAkhirTxt.datebox({
	width: "120px",
	prompt: "Tgl. Akhir",
	formatter: dateFormatter,
	parser: dateParser,
});

searchBt.linkbutton({
	iconCls: "icon-search",
	onClick: doSearch,
});

resetBt.linkbutton({
	iconCls: "icon-reload",
	onClick: () => {
		nosamwTxt.textbox("setValue", "");
		tglAwalTxt.datebox("setValue", "");
		tglAkhirTxt.datebox("setValue", "");
		doSearch();
	},
});

function doSearch() {
	const nosamw = nosamwTxt.textbox("getValue");
	const tglAwal = tglAwalTxt.datebox("getValue");
	const tglAkhir = tglAkhirTxt.datebox("getValue");

	if (!nosamw || !tglAwal || !tglAkhir) {
		showErrorToast("No. Pelanggan, Tgl. Awal, atau Tgl. Akhir harus diisi!");
		return;
	}

	dg.datagrid("load", {
		nosamw,
		tglAwal,
		tglAkhir,
	});
}

function showErrorToast(message) {
	Toastify({
		text: message,
		backgroundColor: "red",
	}).showToast();
}

dg.datagrid({
	title: "Verif Baca Meter",
	url: apiUri,
	method: "GET",
	fitColumns: true,
	singleSelect: true,
	rownumbers: true,
	height: "auto",
	columns: [
		[
			{ field: "nosamw", title: "No. Pelanggan", width: 100 },
			{ field: "nama", title: "Nama. Pelanggan", width: 200 },
			{ field: "tgl", title: "Tgl. Baca", width: 80 },
			{ field: "tgl_upload", title: "Tgl. Upload", width: 120 },
			{ field: "stan_kini", title: "Stan Kini", width: 80 },
			{ field: "stan_lalu", title: "Stan Lalu", width: 80 },
			{ field: "pakai", title: "Pemakaian", width: 80 },
			{ field: "kondisi", title: "Kondisi WM", width: 120, sortable: true },
			{ field: "ket", title: "Keterangan", width: 180 },
			{ field: "petugas", title: "Petugas", width: 120 },
		],
	],
	resizable: true,
	view: detailview,
	detailFormatter: (index, row) => {
		return '<div class="ddv"></div>';
	},
	onExpandRow: (index, row) => {
		const ddv = dg.datagrid("getRowDetail", index).find("div.ddv");
		ddv.datagrid({
			method: "GET",
			url: `${apiUri}/detail/${row.id}`,
			fitColumns: true,
			singleSelect: true,
			columns: [
				[
					{
						field: "foto",
						title: "FOTO",
						width: "310px",
						formatter: imageFormatter,
					},
					{
						field: "id",
						title: "Detail",
						formatter: (value, row, index) => {
							return `
                                <table class="detail-foto">
                                    <tr>
                                        <td>Tgl Upload</td>
                                        <td>: ${row.tgl}</td>
                                    </tr>
                                    <tr>
                                        <td>Tgl Baca</td>
                                        <td>: ${row.info}</td>
                                    </tr>
                                    <tr>
                                        <td>Stan Kini</td>
                                        <td>: ${row.stan_kini}</td>
                                    </tr>
                                </table>`;
						},
					},
					// { field: "ket", title: "KETERANGAN", width: "100%" },
				],
			],
			onResize: () => {
				dg.datagrid("fixDetailRowHeight", index);
				dg.datagrid("fixRowHeight");
			},
			onLoadSuccess: () => {
				setTimeout(() => {
					dg.datagrid("fixDetailRowHeight", index);
					dg.datagrid("fixRowHeight");
				}, 100);
			},
		});
		dg.datagrid("fixDetailRowHeight", index);
		dg.datagrid("fixRowHeight");
	},
});
