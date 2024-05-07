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
	const periode = `${bulanOpt.combobox("getText")} ${tahunOpt.combobox(
		"getText",
	)}`;
	dg.datagrid("print", {
		title: "Rekap Kondisi Water Meter Per Cabang",
		caption: `<h3>Rekap Kondisi Water Meter Per Cabang</h3>
		<h4>Periode : ${periode}</h4>`,
	});
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
			...listField,
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
