const $dg = $("#dg");
const $nosamwTxt = $("#nosamw");
const $cabangOpt = $("#cabang");
const $petugasOpt = $("#petugas");
const $kampungOpt = $("#kampung");
const $cekOpt = $("#cek");
const $tahunOpt = $("#tahun");
const $bulanOpt = $("#bulan");
const $kondisiOpt = $("#kondisi");
const $searchBt = $("#search");
const $resetBt = $("#reset");
const $excelBt = $("#excel");
const apiUri = `${baseUri}/api/laporan/hasil_baca`;

$nosamwTxt.textbox({
	width: "120px",
	prompt: "No. Pelanggan",
});
$cabangOpt.combobox({
	width: "180px",
	prompt: "Cabang",
	onChange: findPegawaiList,
});
$petugasOpt.combobox({
	width: "120px",
	prompt: "Petugas",
	url: `${baseUri}/api/sikompak/pegawai`,
	method: "GET",
	valueField: "user",
	textField: "user",
	onChange: findKampungByPetugas,
});
$kampungOpt.combobox({
	width: "120px",
	prompt: "Kampung",
	url: `${baseUri}/api/sikompak/pegawai/kampung`,
	method: "GET",
	valueField: "nik",
	textField: "nik",
});
$cekOpt.combobox({ width: "150px", prompt: "Cek" });
$tahunOpt.combobox({ width: "80px", prompt: "Tahun" });
$bulanOpt.combobox({ width: "120px", prompt: "Bulan" });
$kondisiOpt.combobox({ width: "150px" });
$searchBt.linkbutton({ iconCls: "icon-search", onClick: doSearch });
$resetBt.linkbutton({ iconCls: "icon-reload", onClick: resetFormAndSearch });
$excelBt.linkbutton({ iconCls: "icon-print", onClick: doExcel });

function resetFormAndSearch() {
	resetFormFields();
	setDefaultValues();
	doSearch();
}

function resetFormFields() {
	$nosamwTxt.textbox("clear");
	$cabangOpt.combobox("clear");
	$petugasOpt.combobox("clear");
	$kampungOpt.combobox("clear");
	$kondisiOpt.combobox("clear");
}

function setDefaultValues() {
	const currentYear = new Date().getFullYear();
	const currentMonth = new Date().getMonth() + 1;
	$tahunOpt.combobox("setValue", currentYear);
	$bulanOpt.combobox("setValue", currentMonth);
	$cekOpt.combobox("setValue", "0");
}

$dg.datagrid({
	title: "Verif Baca Meter",
	url: apiUri,
	method: "GET",
	columns: [
		[
			{ field: "no_sam", title: "No. Pelanggan", width: 100 },
			{ field: "nama", title: "Nama. Pelanggan", width: 200 },
			{ field: "stan_kini", title: "Stan Kini", width: 80 },
			{ field: "stan_lalu", title: "Stan Lalu", width: 80 },
			{ field: "pakai", title: "Pemakaian", width: 80 },
			{ field: "kondisi", title: "Kondisi WM", width: 120, sortable: true },
			{ field: "ket", title: "Keterangan", width: 180 },
			{ field: "tgl", title: "Tgl. Upload", width: 80 },
			{ field: "info", title: "Tgl. Baca", width: 120 },
			{ field: "petugas", title: "Petugas", width: 120 },
		],
	],
	rownumbers: true,
	resizable: true,
	pagination: true,
});

function doSearch() {
	const searchParam = getSearchParams();
	$dg.datagrid("reload", searchParam);
}

function getSearchParams() {
	const searchParam = {};
	const tahun = $tahunOpt.combobox("getValue");
	const bulan = $bulanOpt.combobox("getValue");
	const periode = `${tahun}-${bulan}`;
	setSearchParam(searchParam, "nosamw", $nosamwTxt);
	setSearchParam(searchParam, "cabang", $cabangOpt);
	setSearchParam(searchParam, "petugas", $petugasOpt);
	setSearchParam(searchParam, "kampung", $kampungOpt);
	setSearchParam(searchParam, "kondisi", $kondisiOpt);
	Object.assign(searchParam, { periode, cek: $cekOpt.combobox("getValue") });
	return searchParam;
}

function setSearchParam(searchParam, key, element) {
	const value =
		element.prop("tagName") === "INPUT"
			? element.val()
			: element.combobox("getValue");
	if (value) {
		Object.assign(searchParam, { [key]: value });
	}
}

function findPegawaiList(cabang) {
	const searchParam = new URLSearchParams();
	if (cabang) searchParam.append("cabang", cabang);
	$petugasOpt.combobox(
		"reload",
		`${baseUri}/api/sikompak/pegawai?${searchParam}`,
	);
	manipulateKampung(searchParam);
}

function manipulateKampung(searchParam) {
	$kampungOpt.combobox(
		"reload",
		`${baseUri}/api/sikompak/pegawai/kampung?${searchParam}`,
	);
}

function findKampungByPetugas(petugas) {
	const searchParam = new URLSearchParams();
	if ($cabangOpt.combobox("getValue"))
		searchParam.append("cabang", $cabangOpt.combobox("getValue"));
	if (petugas) searchParam.append("petugas", petugas);
	manipulateKampung(searchParam);
}

function doExcel() {
	const searchParam = getSearchParams();
	if (searchParam.cabang === "") {
		alert("Cabang harus dipilih");
		return;
	}
	Object.assign(searchParam, { time: new Date().getTime() });
	window.open(
		`${baseUri}/cetak/detail_hasil_baca?${new URLSearchParams(
			searchParam,
		).toString()}`,
	);
}
