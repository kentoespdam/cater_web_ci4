const dg = $("#dg");
const pg = $("#pg");
const tahunOpt = $("#tahun");
const bulanOpt = $("#bulan");
const cabangOpt = $("#cabang");
const searchBySwitch = $("#searchBy");
const petugasOpt = $("#petugas");
const searchBt = $("#search");
const resetBt = $("#reset");
const cetakBt = $("#cetak");
const apiUri = `${baseUri}/api/selisih`;

tahunOpt.combobox({
	width: "80px",
	label: "Tahun",
	labelPosition: "top",
	prompt: "Tahun",
});
bulanOpt.combobox({
	width: "120px",
	label: "Bulan",
	labelPosition: "top",
	prompt: "Bulan",
});
cabangOpt.combobox({
	width: "180px",
	label: "Cabang",
	labelPosition: "top",
});
searchBySwitch.switchbutton({
	label: "Cari Berdasar Cabang",
	labelPosition: "top",
	onChange(value) {
		if (value) {
			cabangOpt.combobox("enable");
			petugasOpt.combobox("disable");
		} else {
			cabangOpt.combobox("disable");
			petugasOpt.combobox("enable");
		}
	},
});

petugasOpt.combobox({
	width: "180px",
	label: "Petugas",
	labelPosition: "top",
	prompt: "Petugas",
});
cabangOpt.combobox("disable");
petugasOpt.combobox("enable");

searchBt.linkbutton({ iconCls: "icon-search", onClick: doSearch });
resetBt.linkbutton({ iconCls: "icon-reload", onClick: resetFormAndSearch });
cetakBt.linkbutton({
	iconCls: "icon-print",
	onClick: doCetak,
});

function doSearch() {
	const tahun = tahunOpt.combobox("getValue");
	const bulan = bulanOpt.combobox("getValue");
	const searchBy = searchBySwitch.switchbutton("options").checked;
	const petugas = petugasOpt.combobox("getValue");
	const cabang = cabangOpt.combobox("getValue");
	if (!cabang && !petugas) {
		alert("Cabang atau petugas harus dipilih");
		return;
	}
	const params = {
		tahun: tahun,
		bulan: bulan,
		petugas: petugas,
		cabang: cabang,
	};
	if (searchBy) Object.assign(params, { findByCabang: "on" });
	dg.datagrid("load", params);
	dg.datagrid("resize");
}

function resetFormAndSearch() {}

function doCetak() {
	const tahun = tahunOpt.combobox("getValue");
	const bulan = bulanOpt.combobox("getValue");
	const searchBy = searchBySwitch.switchbutton("options").checked;
	const petugas = petugasOpt.combobox("getValue");
	const cabang = cabangOpt.combobox("getValue");
	if (!cabang && !petugas) {
		alert("Cabang atau petugas harus dipilih");
		return;
	}
	const params = {
		tahun: tahun,
		bulan: bulan,
		petugas: petugas,
		cabang: cabang,
	};
	if (searchBy) Object.assign(params, { findByCabang: "on" });
	const searchParam = new URLSearchParams(params).toString();
	window.open(`${baseUri}/cetak/selisih_foto?${searchParam}`);
}

dg.datagrid({
	title: "Cek data belum ada foto",
	url: apiUri,
	method: "GET",
	columns: [
		[
			{ field: "cabang", title: "Cabang", width: "120" },
			{ field: "petugas", title: "Petugas", width: "100" },
			{ field: "nosamw", title: "No. Pelanggan", width: "100" },
			{ field: "nama", title: "Nama", width: "180" },
			{ field: "kampung", title: "Kampung", width: "150" },
			{ field: "alamat", title: "Alamat" },
		],
	],
	rownumbers: true,
	resizable: true,
	onLoadSuccess: (data) => {
		console.log(data);
		pg.propertygrid("loadData", data.propertygrid);
	},
});

pg.propertygrid({
	url: `${baseUri}/api/selisih/default_property`,
	method: "GET",
});
