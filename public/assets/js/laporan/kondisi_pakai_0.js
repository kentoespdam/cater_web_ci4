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
				field: "Normal",
				title: "Normal",
				align: "right",
				styler: (value) => "background-color:green;color:white",
			},
			{
				field: "WM Mati",
				title: "WM Mati",
				align: "right",
			},
			{
				field: "Angka tidak wajar",
				title: "Angka tidak wajar",
				align: "right",
			},
			{
				field: "Stand Tunggu",
				title: "Stand Tunggu",
				align: "right",
			},
			{
				field: "Stand Mundur",
				title: "Stand Mundur",
				align: "right",
			},
			{
				field: "Ganti WM",
				title: "Ganti WM",
				align: "right",
			},
			{
				field: "Pencurian Air",
				title: "Pencurian Air",
				align: "right",
			},
			{
				field: "Tidak Mengalir",
				title: "Tidak Mengalir",
				align: "right",
			},
			{
				field: "Tidak Ketemu Alamat",
				title: "Tidak Ketemu Alamat",
				align: "right",
			},
			{
				field: "Meter Embun",
				title: "Meter Embun",
				align: "right",
			},
			{
				field: "Meter Pecah",
				title: "Meter Pecah",
				align: "right",
			},
			{
				field: "Meter Buram",
				title: "Meter Buram",
				align: "right",
			},
			{
				field: "Meter Terpendam",
				title: "Meter Terpendam",
				align: "right",
			},
			{
				field: "Meter Tertimbun",
				title: "Meter Tertimbun",
				align: "right",
			},
			{
				field: "Edit Stand OCR",
				title: "Edit Stand OCR",
				align: "right",
			},
			{
				field: "Ada Anjing",
				title: "Ada Anjing",
				align: "right",
			},
			{
				field: "Pelanggan Tempel Angka",
				title: "Pelanggan Tempel Angka",
				align: "right",
			},
			{
				field: "Pagar Kunci",
				title: "Pagar Kunci",
				align: "right",
			},
			{
				field: "Segel Putus",
				title: "Segel Putus",
				align: "right",
			},
			{
				field: "Box Meter Terkunci",
				title: "Box Meter Terkunci",
				align: "right",
			},
			{
				field: "Meter Macet",
				title: "Meter Macet",
				align: "right",
			},
			{
				field: "Meter Tera",
				title: "Meter Tera",
				align: "right",
			},
			{
				field: "Meter Terbalik",
				title: "Meter Terbalik",
				align: "right",
			},
			{
				field: "Air Tidak Terpakai",
				title: "Air Tidak Terpakai",
				align: "right",
			},
			{
				field: "Lain-Lain",
				title: "Lain-Lain",
				align: "right",
			},
			{
				field: "unknown",
				title: "Tanpa Keterangan",
				align: "right",
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
