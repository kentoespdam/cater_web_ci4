const dg = $("#dg");
const tahunOpt = $("#tahun");
const bulanOpt = $("#bulan");
const searchBt = $("#search");
const printBt = $("#print");
const excelBt = $("#excel");
const apiUri = `${baseUri}/api/laporan/kondisi`;

tahunOpt.combobox({ width: "80px", prompt: "Tahun" });
bulanOpt.combobox({ width: "120px", prompt: "Bulan" });
searchBt.linkbutton({ iconCls: "icon-search", onClick: doSearch });
printBt.linkbutton({ iconCls: "icon-print", onClick: doPrint });
excelBt.linkbutton({ iconCls: "icon-print", onClick: doExcel });

function doSearch() {
	const tahun = tahunOpt.combobox("getValue");
	const bulan = bulanOpt.combobox("getValue");
	dg.datagrid("load", { tahun, bulan });
	dg.datagrid("resize");
}

function doPrint() {
	const periode = `${bulanOpt.combobox("getText")} ${tahunOpt.combobox(
		"getText",
	)}`;
	dg.datagrid("print", {
		title: "Rekap Kondisi Water Meter Per Cabang",
		caption: `<h3>Rekap Kondisi Water Meter Per Cabang</h3>
		<h4>Periode : ${periode}</h4>`,
	});
}

function doExcel() {
	const tahun = tahunOpt.combobox("getValue");
	const bulan = bulanOpt.combobox("getValue");
	const params = {
		time: new Date().getTime(),
		tahun: tahun,
		bulan: bulan,
	};
	const searchParam = new URLSearchParams(params).toString();
	window.open(`${baseUri}/cetak/rekap_kondisi_baca?${searchParam}`);
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
				field: "Normal",
				title: "Normal",
				align: "right",
				styler: (value) => "background-color:green; color:white;",
			},
			{
				field: "WM Mati",
				title: "WM Mati",
				align: "right",
				styler: (value) => "background-color:yellow",
			},
			{
				field: "Angka tidak wajar",
				title: "Angka tidak wajar",
				align: "right",
				styler: (value) => "background-color:yellow",
			},
			{
				field: "Stand Tunggu",
				title: "Stand Tunggu",
				align: "right",
				styler: (value) => "background-color:yellow",
			},
			{
				field: "Stand Mundur",
				title: "Stand Mundur",
				align: "right",
				styler: (value) => "background-color:yellow",
			},
			{
				field: "Ganti WM",
				title: "Ganti WM",
				align: "right",

				styler: (value) => "background-color:yellow",
			},
			{
				field: "Pencurian Air",
				title: "Pencurian Air",
				align: "right",

				styler: (value) => "background-color:yellow",
			},
			{
				field: "Tidak Mengalir",
				title: "Tidak Mengalir",
				align: "right",

				styler: (value) => "background-color:yellow",
			},
			{
				field: "Tidak Ketemu Alamat",
				title: "Tidak Ketemu Alamat",
				align: "right",

				styler: (value) => "background-color:yellow",
			},
			{
				field: "Meter Embun",
				title: "Meter Embun",
				align: "right",

				styler: (value) => "background-color:yellow",
			},
			{
				field: "Meter Pecah",
				title: "Meter Pecah",
				align: "right",

				styler: (value) => "background-color:yellow",
			},
			{
				field: "Meter Buram",
				title: "Meter Buram",
				align: "right",

				styler: (value) => "background-color:yellow",
			},
			{
				field: "Meter Terpendam",
				title: "Meter Terpendam",
				align: "right",

				styler: (value) => "background-color:yellow",
			},
			{
				field: "Meter Tertimbun",
				title: "Meter Tertimbun",
				align: "right",

				styler: (value) => "background-color:yellow",
			},
			{
				field: "Edit Stand OCR",
				title: "Edit Stand OCR",
				align: "right",

				styler: (value) => "background-color:yellow",
			},
			{
				field: "Ada Anjing",
				title: "Ada Anjing",
				align: "right",

				styler: (value) => "background-color:yellow",
			},
			{
				field: "Pelanggan Tempel Angka",
				title: "Pelanggan Tempel Angka",
				align: "right",

				styler: (value) => "background-color:yellow",
			},
			{
				field: "Pagar Kunci",
				title: "Pagar Kunci",
				align: "right",

				styler: (value) => "background-color:yellow",
			},
			{
				field: "Segel Putus",
				title: "Segel Putus",
				align: "right",

				styler: (value) => "background-color:yellow",
			},
			{
				field: "Box Meter Terkunci",
				title: "Box Meter Terkunci",
				align: "right",

				styler: (value) => "background-color:yellow",
			},
			{
				field: "Meter Macet",
				title: "Meter Macet",
				align: "right",

				styler: (value) => "background-color:yellow",
			},
			{
				field: "Meter Tera",
				title: "Meter Tera",
				align: "right",

				styler: (value) => "background-color:yellow",
			},
			{
				field: "Meter Terbalik",
				title: "Meter Terbalik",
				align: "right",

				styler: (value) => "background-color:yellow",
			},
			{
				field: "Air Tidak Terpakai",
				title: "Air Tidak Terpakai",
				align: "right",

				styler: (value) => "background-color:yellow",
			},
			{
				field: "Lain-Lain",
				title: "Lain-Lain",
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
				styler: (value) => "background-color:green; color:white;",
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
