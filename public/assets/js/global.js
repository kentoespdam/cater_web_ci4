const baseUri = window.location.origin;

function formatDecimal(num) {
	return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
}

function formatPersen(num) {
	return `${num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")}%`;
}

function dateFormatter(date) {
	const y = date.getFullYear();
	let m = date.getMonth() + 1;
	m = m < 10 ? `0${m}` : m;
	let d = date.getDate();
	d = d < 10 ? `0${d}` : d;
	return `${y}-${m}-${d}`;
}

function dateParser(date) {
	const t = Date.parse(date);
	if (!Number.isNaN(t)) return new Date(t);
	return new Date();
}

function imageFormatter(img, row) {
	if (!img)
		return `<div style="min-height: 300px; "><img src="${baseUri}/assets/images/no-image.png" id="zoomify-${row.id}" width="300"></div>`;
	return `<img src="data:image/jpg;base64, ${img}" id="zoomify-${row.id}" width="300" height="350">`;
}

$.extend($.fn.combobox.methods, {
	removeme: (jq) =>
		jq.each(function () {
			const state = $.data(this, "combobox");
			if (state) {
				$.removeData(this, "combobox");
				$(this).removeClass("combobox-f");
			}
			$(this).combo("removeme");
		}),
});
