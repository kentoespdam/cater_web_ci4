const dg = $("#dg");
const nosamwTxt = $("#nosamw");
const tglAwalTxt = $("#tglAwal");
const tglAkhirTxt = $("#tglAkhir");
const searchBt = $("#search");
const resetBt = $("#reset");
const cetakBt = $("#cetak");
const apiUri = `${baseUri}/api/cekfoto`;

nosamwTxt.textbox({
	width: "120px",
	prompt: "No. Pelanggan",
});

tglAwalTxt.datebox({
	width: "120px",
	prompt: "Tgl. Awal",
	formatter: dateFormatter,
	parser: dateParser,
});

tglAkhirTxt.datebox({
	width: "120px",
	prompt: "Tgl. Akhir",
	formatter: dateFormatter,
	parser: dateParser,
});

searchBt.linkbutton({
	iconCls: "icon-search",
	onClick: doSearch,
});

resetBt.linkbutton({
	iconCls: "icon-reload",
	onClick: () => {
		nosamwTxt.textbox("setValue", "");
		tglAwalTxt.datebox("setValue", "");
		tglAkhirTxt.datebox("setValue", "");
		doSearch();
	},
});

cetakBt.linkbutton({
	iconCls: "icon-print",
	onClick: () => {
		const nosamw = nosamwTxt.textbox("getValue");
		const tglAwal = tglAwalTxt.datebox("getValue");
		const tglAkhir = tglAkhirTxt.datebox("getValue");
		window.open(`${baseUri}/cetak/foto_meter/${tglAwal}/${tglAkhir}/${nosamw}`);
	},
});

function doSearch() {
	const nosamw = nosamwTxt.textbox("getValue");
	const tglAwal = tglAwalTxt.datebox("getValue");
	const tglAkhir = tglAkhirTxt.datebox("getValue");

	if (!nosamw || !tglAwal || !tglAkhir) {
		showErrorToast("No. Pelanggan, Tgl. Awal, atau Tgl. Akhir harus diisi!");
		return;
	}

	dg.datagrid("load", {
		nosamw,
		tglAwal,
		tglAkhir,
	});
}

function showErrorToast(message) {
	Toastify({
		text: message,
		style: {
			background: "red",
		},
	}).showToast();
}

dg.datagrid({
	title: "Verif Baca Meter",
	url: apiUri,
	method: "GET",
	fitColumns: true,
	singleSelect: true,
	rownumbers: true,
	height: "auto",
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
	resizable: true,
	view: detailview,
	detailFormatter: (index, row) => {
		return '<div class="ddv"></div>';
	},
	onExpandRow: (index, row) => {
		const ddv = dg.datagrid("getRowDetail", index).find("div.ddv");
		ddv.datagrid({
			method: "GET",
			url: `${apiUri}/detail/${row.id}`,
			fitColumns: true,
			singleSelect: true,
			columns: [
				[
					{
						field: "foto",
						title: "FOTO",
						width: "310px",
						formatter: imageFormatter,
					},
					{
						field: "id",
						title: "Detail",
						formatter: (value, row, index) => {
							return `
                                <table class="detail-foto">
                                    <tr>
                                        <td>Tgl Upload</td>
                                        <td>: ${row.tgl}</td>
                                    </tr>
                                    <tr>
                                        <td>Tgl Baca</td>
                                        <td>: ${row.info}</td>
                                    </tr>
                                    <tr>
                                        <td>Stan Kini</td>
                                        <td>: ${row.stan_kini}</td>
                                    </tr>
                                </table>`;
						},
					},
					// { field: "ket", title: "KETERANGAN", width: "100%" },
				],
			],
			onResize: () => {
				dg.datagrid("fixDetailRowHeight", index);
				dg.datagrid("fixRowHeight");
			},
			onLoadSuccess: () => {
				setTimeout(() => {
					dg.datagrid("fixDetailRowHeight", index);
					dg.datagrid("fixRowHeight");
				}, 100);
			},
		});
		dg.datagrid("fixDetailRowHeight", index);
		dg.datagrid("fixRowHeight");
	},
});

window.previewFoto = function (el) {
	const imgEl = $(el);
	const id = imgEl.data("id");
	const tgl = imgEl.data("tgl") || "foto";
	const nosamw = imgEl.data("nosamw") || id;

	const viewer = new Viewer(el, {
		inline: false,
		toolbar: {
			zoomIn: true,
			zoomOut: true,
			oneToOne: true,
			reset: true,
			prev: false,
			next: false,
			rotateLeft: true,
			rotateRight: true,
			flipHorizontal: false,
			flipVertical: false,
			download: function () {
				const currentSrc = (viewer.image && viewer.image.src) ? viewer.image.src : imgEl.attr("src");
				const a = document.createElement("a");
				a.href = currentSrc;
				a.download = `foto_meter_${nosamw}_${tgl}.jpg`;
				document.body.appendChild(a);
				a.click();
				document.body.removeChild(a);
			},
		},
		hidden: function () {
			viewer.destroy();
		},
	});

	viewer.show();

	Toastify({
		text: "Memuat foto original...",
		style: {
			background: "#3498db",
		},
		duration: 3000,
	}).showToast();

	$.ajax({
		url: `${apiUri}/detail/${id}?original=1`,
		method: "GET",
		dataType: "json",
		success: function (res) {
			if (res && res.length > 0 && res[0].foto) {
				const origBase64 = res[0].foto;
				const origSrc = 'data:image/jpeg;base64,' + origBase64.trim();
				
				imgEl.attr('src', origSrc);
				if (viewer && viewer.image) {
					viewer.image.src = origSrc;
				}
				if (viewer && typeof viewer.update === "function") {
					viewer.update();
				}

				Toastify({
					text: "Foto resolusi original dimuat",
					style: {
						background: "green",
					},
					duration: 3000,
				}).showToast();
			} else {
				Toastify({
					text: "Gagal memuat foto original",
					style: {
						background: "orange",
					},
					duration: 3000,
				}).showToast();
			}
		},
		error: function () {
			Toastify({
				text: "Gagal memuat foto original (Network Error)",
				style: {
					background: "red",
				},
				duration: 3000,
			}).showToast();
		},
	});
};


