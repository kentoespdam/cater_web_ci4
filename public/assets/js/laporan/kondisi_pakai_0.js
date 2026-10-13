const dg = $("#dg");
const tahunOpt = $("#tahun");
const bulanOpt = $("#bulan");
const searchBt = $("#search");
const printBt = $("#print");
const apiUri = `${baseUri}/api/laporan/kondisi_pakai_0`;

tahunOpt.combobox({ width: "80px", prompt: "Tahun" });
bulanOpt.combobox({ width: "120px", prompt: "Bulan" });
searchBt.linkbutton({ iconCls: "icon-search", onClick: doSearch });
printBt.linkbutton({ iconCls: "icon-print", onClick: doPrint });

function doSearch() {
	const tahun = tahunOpt.combobox("getValue");
	const bulan = bulanOpt.combobox("getValue");
	dg.datagrid("load", { tahun, bulan });
	dg.datagrid("resize");
}

function doPrint() {
	const tahun = tahunOpt.combobox("getValue");
	const bulan = bulanOpt.combobox("getValue");
	const params = {
		time: new Date().getTime(),
		tahun: tahun,
		bulan: bulan,
	};
	const searchParam = new URLSearchParams(params).toString();
	window.open(`${baseUri}/cetak/rekap_kondisi_baca_0?${searchParam}`);
}

const listField = listKondisi.map((kondisi) => {
	const style =
		kondisi.kondisi === "Normal" ? "background-color:green;color:white" : "";
	return {
		field: kondisi.kondisi,
		title: kondisi.kondisi,
		formatter: formatDecimal,
		align: "right",
		styler: (value) => {
			return style;
		},
	};
});

dg.datagrid({
	title: "Rekap Kondisi Water Meter Per Cabang",
	url: apiUri,
	method: "GET",
	columns: [
		[
			{ field: "periode", title: "Periode", width: "100" },
			{ field: "cabang", title: "Cabang", width: "150" },
			{
				field: "normal",
				title: "Normal",
				align: "right",
				styler: (value) => "background-color:green; color:white;",
			},
			{
				field: "mati",
				title: "Mati",
				align: "right",
				styler: (value) => "background-color:yellow",
			},
			{
				field: "stand tunggu",
				title: "Stand Tunggu",
				align: "right",
				styler: (value) => "background-color:yellow",
			},
			{
				field: "stand mundur",
				title: "Stand Mundur",
				align: "right",
				styler: (value) => "background-color:yellow",
			},
			{
				field: "ganti wm",
				title: "Ganti WM",
				align: "right",
				styler: (value) => "background-color:yellow",
			},
			{
				field: "pencurian air",
				title: "Pencurian Air",
				align: "right",
				styler: (value) => "background-color:yellow",
			},
			{
				field: "tidak terjangkau",
				title: "Tidak Terjangkau",
				align: "right",
				styler: (value) => "background-color:yellow",
			},
			{
				field: "berembun",
				title: "Berembun",
				align: "right",
				styler: (value) => "background-color:yellow",
			},
			{
				field: "hilang",
				title: "Hilang",
				align: "right",
				styler: (value) => "background-color:yellow",
			},
			{
				field: "rusak",
				title: "Rusak",
				align: "right",
				styler: (value) => "background-color:yellow",
			},
			{
				field: "buram",
				title: "Buram",
				align: "right",
				styler: (value) => "background-color:yellow",
			},
			{
				field: "terpendam",
				title: "Terpendam",
				align: "right",
				styler: (value) => "background-color:yellow",
			},
			{
				field: "tempel angka",
				title: "Tempel Angka",
				align: "right",
				styler: (value) => "background-color:yellow",
			},
			{
				field: "pagar kunci",
				title: "Pagar Kunci",
				align: "right",
				styler: (value) => "background-color:yellow",
			},
			{
				field: "segel putus",
				title: "Segel Putus",
				align: "right",
				styler: (value) => "background-color:yellow",
			},
			{
				field: "cabut meter",
				title: "Cabut Meter",
				align: "right",
				styler: (value) => "background-color:yellow",
			},

			{
				field: "unknown",
				title: "Tanpa Keterangan",
				align: "right",

				styler: (value) => "background-color:yellow",
			},
			{
				field: "total",
				title: "Total",
				width: "100",
				align: "right",
				formatter: formatDecimal,
			},
		],
	],
	rownumbers: true,
	resizable: true,
	nowrap: false,
	showFooter: true,
	onLoadSuccess: (data) => {
		dg.datagrid("reloadFooter", data.footer);
	},
	renderFooter: (data) => {
		return data.footer;
	},
});
