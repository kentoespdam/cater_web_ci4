const dg = $("#dg");
const tahunOpt = $("#tahun");
const bulanOpt = $("#bulan");
const searchBt = $("#search");
const printBt = $("#print");
const apiUri = `${baseUri}/api/laporan/target`;

tahunOpt.combobox({ width: "80px", prompt: "Tahun" });
bulanOpt.combobox({ width: "120px", prompt: "Bulan" });
searchBt.linkbutton({ iconCls: "icon-search", onClick: doSearch });
printBt.linkbutton({ iconCls: "icon-print", onClick: doPrint });

function doSearch() {
	const tahun = tahunOpt.combobox("getValue");
	const bulan = bulanOpt.combobox("getValue");
	$("#periode").text(
		`${bulanOpt.combobox("getText")} ${tahunOpt.combobox("getValue")}`,
	);
	dg.datagrid("load", { tahun, bulan });
	dg.datagrid("resize");
}

function doPrint() {
	const periode = `${bulanOpt.combobox("getText")} ${tahunOpt.combobox(
		"getText",
	)}`;
	dg.datagrid("print", {
		title: "Rekap Target Cater Per Cabang",
		caption: `<h3>Rekap Target Cater Per Cabang</h3>
		<h4>Periode : ${periode}</h4>`,
	});
}

dg.datagrid({
	title: "Rekap Target Cater Per Cabang",
	url: apiUri,
	method: "GET",
	columns: [
		[
			{ field: "periode", title: "Periode", width: "100" },
			{ field: "cabang", title: "Cabang", width: "150" },
			{
				field: "jml_baca",
				title: "WM Dibaca",
				width: "100",
				align: "right",
				formatter: formatDecimal,
			},
			{
				field: "sukses",
				title: "Sukses",
				width: "80",
				align: "right",
				formatter: formatDecimal,
			},
			{
				field: "gagal",
				title: "Gagal",
				width: "80",
				align: "right",
				formatter: formatDecimal,
			},
			{
				field: "kondisi",
				title: "Kondisi Meter",
				width: "100",
				align: "right",
				formatter: formatDecimal,
			},
			{
				field: "persen",
				title: "Sukses/Input",
				width: "100",
				align: "right",
				formatter: formatPersen,
			},
		],
	],
	rownumbers: true,
	resizable: true,
	showFooter: true,
	onLoadSuccess: (data) => {
		dg.datagrid("reloadFooter", data.footer);
	},
	renderFooter: (data) => {
		return data.footer;
	},
});
