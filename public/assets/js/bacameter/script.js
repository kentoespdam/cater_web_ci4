const dg = $("#dg");
const tahunOpt = $("#tahun");
const bulanOpt = $("#bulan");
const searchBt = $("#search");
const resetBt = $("#reset");
const apiUri = `${baseUri}/api/hasilbaca`;

tahunOpt.combobox({
	width: "80px",
	label: "Tahun",
	labelPosition: "top",
});
bulanOpt.combobox({
	label: "Bulan",
	labelPosition: "top",
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
			{ field: "nama", title: "Nama Petugas", width: "180" },
			{ field: "cabang", title: "Cabang", width: "120" },
			{
				field: "progress",
				title: "Progress",
				align: "right",
				formatter: (progress) => {
					return `${progress}%`;
				},
				styler: (progress) => {
					return progress >= 100 ? "color:green" : "color:red";
				},
			},
			{
				field: "jml_pelanggan",
				title: "Jml Pelanggan",
				align: "right",
				formatter: formatDecimal,
			},
			{
				field: "jml_baca",
				title: "Jml Dibaca",
				align: "right",
				formatter: formatDecimal,
			},
			{
				field: "cek_koperasi",
				title: "Cek Koperasi",
				align: "right",
				formatter: formatDecimal,
			},
			{
				field: "cek_cabang",
				title: "Cek Cabang",
				align: "right",
				formatter: formatDecimal,
			},
			{
				field: "gagal",
				title: "Gagal",
				align: "right",
				formatter: formatDecimal,
				styler: (failureCount) => {
					return failureCount > 0
						? "background:red; color:white"
						: "color:green";
				},
			},
		],
	],
	rownumbers: true,
	resizable: true,
	showFooter: true,
	view: detailview,
	detailFormatter: (index, row) => {
		return '<div class="ddv" style="padding:5px 0"></div>';
	},
	onExpandRow: (index, row) => {
		const tahun = tahunOpt.combobox("getValue");
		const bulan = bulanOpt.combobox("getValue");
		const periode = `${tahun}-${bulan}`;
		const ddv = dg.datagrid("getRowDetail", index).find("div.ddv");
		ddv.datagrid({
			method: "GET",
			url: `${apiUri}/gagal?periode=${periode}&petugas=${row.petugas}`,
			fitColumns: true,
			singleSelect: true,
			rownumbers: true,
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
	onLoadSuccess: (data) => {
		const totalProgress = (
			(data.footer.jml_baca / data.footer.jml_pelanggan) *
			100
		).toFixed(2);
		const footer = `
			<tr>
				<th colspan="2">Total</th>
				<th>${totalProgress}</th>
				<th>${data.footer.jml_pelanggan}</th>
				<th>${data.footer.jml_baca}</th>
				<th>${data.footer.cek_koperasi}</th>
				<th>${data.footer.cek_cabang}</th>
				<th>${data.footer.gagal}</th>
			</tr>
		`;
		dg.datagrid("reloadFooter", [
			{
				progress: totalProgress,
				jml_pelanggan: data.footer.jml_pelanggan,
				jml_baca: data.footer.jml_baca,
				cek_koperasi: data.footer.cek_koperasi,
				cek_cabang: data.footer.cek_cabang,
				gagal: data.footer.gagal,
				footer: footer,
			},
		]);
	},
});

function doSearch() {
	const tahun = tahunOpt.val();
	const bulan = bulanOpt.val();
	dg.datagrid("load", { periode: `${tahun}-${bulan}` });
}
