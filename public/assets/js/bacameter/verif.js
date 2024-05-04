const dg = $("#dg");
const nosamwTxt = $("#nosamw");
const cabangOpt = $("#cabang");
const petugasOpt = $("#petugas");
const kampungOpt = $("#kampung");
const cekOpt = $("#cek");
const tahunOpt = $("#tahun");
const bulanOpt = $("#bulan");
const searchBt = $("#search");
const resetBt = $("#reset");
const apiUri = `${baseUri}/api/verif`;
// console.log(apiUri);

nosamwTxt.textbox({
	width: "120px",
});
cabangOpt.combobox({
	width: "180px",
	onChange: findPegawaiList,
});
petugasOpt.combobox({
	width: "120px",
	url: `${baseUri}/api/sikompak/pegawai`,
	method: "GET",
	valueField: "user",
	textField: "user",
	onChange: findKampungByPetugas,
});
kampungOpt.combobox({
	width: "120px",
	url: `${baseUri}/api/sikompak/pegawai/kampung`,
	method: "GET",
	valueField: "nik",
	textField: "nik",
});
cekOpt.combobox({
	with: "100px",
});
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
});

dg.datagrid({
	title: "Verif Baca Meter",
	url: apiUri,
	method: "GET",
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
	rownumbers: true,
	resizable: true,
	pagination: true,
});

function doSearch() {
	const searchParam = {};
	const tahun = tahunOpt.combobox("getValue");
	const bulan = bulanOpt.combobox("getValue");
	const periode = `${tahun}-${bulan}`;
	if (nosamwTxt.textbox("getValue"))
		Object.assign(searchParam, { nosamw: nosamwTxt.textbox("getValue") });
	if (cabangOpt.combobox("getValue"))
		Object.assign(searchParam, { cabang: cabangOpt.combobox("getValue") });
	if (petugasOpt.combobox("getValue"))
		Object.assign(searchParam, { petugas: petugasOpt.combobox("getValue") });
	if (kampungOpt.combobox("getValue"))
		Object.assign(searchParam, { kampung: kampungOpt.combobox("getValue") });

	Object.assign(searchParam, {
		periode: periode,
	});
	Object.assign(searchParam, { cek: cekOpt.combobox("getValue") });
	dg.datagrid("reload", searchParam);
}

function doReset() {}

function findPegawaiList(cabang) {
	const searchParam = new URLSearchParams();
	petugasOpt.combobox("clear");
	if (cabang !== "") searchParam.append("cabang", cabang);
	petugasOpt.combobox(
		"reload",
		`${baseUri}/api/sikompak/pegawai?${searchParam.toString()}`,
	);
	manipulateKampung(searchParam);
}

function manipulateKampung(searchParam) {
	kampungOpt.combobox("clear");
	kampungOpt.combobox(
		"reload",
		`${baseUri}/api/sikompak/pegawai/kampung?${searchParam.toString()}`,
	);
}

function findKampungByPetugas(petugas) {
	const searchParam = new URLSearchParams();
	searchParam.append("cabang", cabangOpt.combobox("getValue"));
	if (petugas !== "") searchParam.append("petugas", petugas);
	manipulateKampung(searchParam);
}
