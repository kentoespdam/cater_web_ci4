const dg = $("#dg");
const tahunOpt = $("#tahun");
const bulanOpt = $("#bulan");
const searchBt = $("#search");
const resetBt = $("#reset");
const apiUri = `${baseUri}/api/hasilbaca`;

tahunOpt.combobox({
	width: "80px",
});
bulanOpt.combobox({
	width: "120px",
});
searchBt.linkbutton({
	iconCls: "icon-search",
	onClick: doSearch,
});
resetBt.linkbutton({
	iconCls: "icon-reload",
	onClick: () => {
		const tgl = new Date();
		tahunOpt.combobox("setValue", tgl.getFullYear());
		bulanOpt.combobox("setValue", tgl.getMonth() + 1);
		doSearch();
	},
});

dg.datagrid({
	title: "Progress Pembacaan",
	url: apiUri,
	method: "GET",
	columns: [
		[
			{ field: "nama", title: "Nama Petugas", width: "30%" },
			{ field: "cabang", title: "Cabang", width: "25%" },
			{ field: "progress", title: "Progress", align: "right" },
			{ field: "jml_pelanggan", title: "Jml Pelanggan", align: "right" },
			{ field: "jml_baca", title: "Jml Dibaca", align: "right" },
			{ field: "cek_koperasi", title: "Cek Koperasi", align: "right" },
			{ field: "cek_cabang", title: "Cek Cabang", align: "right" },
			{ field: "gagal", title: "Gagal", align: "right" },
		],
	],
	rownumbers: true,
	resizable: true,
	view: detailview,
	detailFormatter: (index, row) => {
		return '<div class="ddv" style="padding:5px 0"></div>';
	},
	onExpandRow: (index, row) => {
		const tahun=tahunOpt.combobox("getValue")
		const bulan=bulanOpt.combobox("getValue")
		const periode=`${tahun}-${bulan}`
		const ddv = dg.datagrid("getRowDetail", index).find("div.ddv");
		ddv.datagrid({
			method: "GET",
			url: `${apiUri}/gagal?periode=${periode}&petugas=${row.petugas}`,
			fitColumns: true,
			singleSelect: true,
			rownumbers: true,
			loadMsg: "",
			height: "auto",
			columns: [
				[
					{ field: "nosamw", title: "No Sambung", width: "25" },
					{ field: "nama", title: "Nama", width: "45%" },
					{ field: "kampung", title: "Kampung", width: "30%" },
				],
			],
			onResize: () => {
				dg.datagrid("fixDetailRowHeight", index);
			},
			onLoadSuccess: () => {
				setTimeout(() => {
					dg.datagrid("fixDetailRowHeight", index);
				}, 100);
			},
		});
		dg.datagrid("fixDetailRowHeight", index);
	},
});

function doSearch() {
	const tahun = tahunOpt.val();
	const bulan = bulanOpt.val();
	dg.datagrid("load", { periode: `${tahun}-${bulan}` });
}

// searchBt.on("click", doSearch);
// resetBt.on("click", () => {
// 	const tgl = new Date();
// 	tahunOpt.val(tgl.getFullYear()).change();
// 	bulanOpt.val(tgl.getMonth() + 1).change();
// });
