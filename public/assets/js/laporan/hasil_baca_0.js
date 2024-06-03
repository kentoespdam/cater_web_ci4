const dg = $("#dg");
const tahunOpt = $("#tahun");
const bulanOpt = $("#bulan");
const cabangOpt = $("#cabang");
const searchBt = $("#search");
const printBt = $("#print");
const apiUri = `${baseUri}/api/laporan/hasil_baca_0`;

tahunOpt.combobox({ width: "80px", prompt: "Tahun" });
bulanOpt.combobox({ width: "120px", prompt: "Bulan" });
cabangOpt.combobox({ width: "180px", prompt: "Cabang" });
searchBt.linkbutton({ iconCls: "icon-search", onClick: doSearch });
printBt.linkbutton({ iconCls: "icon-print", onClick: doPrint });

function doSearch() {
	const tahun = tahunOpt.combobox("getValue");
	const bulan = bulanOpt.combobox("getValue");
	const cabang = cabangOpt.combobox("getValue");
	dg.datagrid("load", { tahun, bulan, cabang });
	dg.datagrid("resize");
}

function doPrint() {
	const tahun = tahunOpt.combobox("getValue");
	const bulan = bulanOpt.combobox("getValue");
	const cabang = cabangOpt.combobox("getValue");
	const params = {
		tahun: tahun,
		bulan: bulan,
	};
	if (cabang) Object.assign(params, { cabang: cabang });
	const searchParam = new URLSearchParams(params).toString();
	window.open(`${baseUri}/cetak/detail_hasil_baca_0?${searchParam}`);
}

dg.datagrid({
	title: "Rekap Bacaan Meter Pemakaian 0",
	url: apiUri,
	method: "GET",
	columns: [
		[
			{ field: "nosamw", title: "No. Sambung", width: 100 },
			{ field: "nama", title: "Pelanggan", width: 200 },
			{ field: "alamat", title: "Alamat" },
			{ field: "stan_kini", title: "Stan Kini", width: 80 },
			{ field: "stan_lalu", title: "Stan Lalu", width: 80 },
			{ field: "pakai", title: "Pemakaian", width: 80 },
			{ field: "petugas", title: "Petugas", width: 120 },
			{ field: "kondisi", title: "Kondisi", width: 120 },
			{ field: "ket", title: "Keterangan", width: 120 },
			{ field: "info", title: "Tgl Baca", width: 120 },
			{ field: "status_cek", title: "Status", width: 120 },
			{ field: "nm_cabang", title: "Cabang", width: 120 },
		],
	],
	rownumbers: true,
	resizable: true,
	nowrap: false,
	showFooter: true,
	pagination: true,
});
