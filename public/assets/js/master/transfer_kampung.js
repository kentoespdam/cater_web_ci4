const srcUser = $("#src_user");
const dstUser = $("#dst_user");
const dstKampung = $("#dst_kampung");
const apiKapumg = `${baseUri}/api/master/user`;
let tmpSrcKampungList = [];
let tmpDstKampungList = [];

for (const kampung of kampungList) {
	const checkboxElement = $(`#src_kampung_${kampung.id}`);
	checkboxElement.checkbox({
		label: kampung.nama,
		value: kampung.id,
		labelWidth: "200px",
		labelPosition: "after",
		
	});
}

srcUser.combobox({
	width: "180px",
	label: "Source Petugas",
	labelPosition: "top",
	prompt: "Petugas",
	onChange: getKampungFrom,
});

dstUser.combobox({
	width: "180px",
	label: "Destination Petugas",
	labelPosition: "top",
	prompt: "Petugas",
	onChange: getKampungTo,
});

async function getKampungFrom() {
	const user = srcUser.combobox("getValue");
	const req = await fetch(`${apiKapumg}/${user}/kampung`);
	const res = await req.json();
	tmpSrcKampungList = [];
	for (const kampung of res) {
		$(`#src_kampung_${kampung.id}`).checkbox("check");
		Object.assign(kampung, { from: "src" });
		tmpSrcKampungList.push(kampung);
	}
	pairKampung();
}

async function getKampungTo() {
	if (tmpSrcKampungList.length === 0) {
		console.log("Source Petugas tidak memiliki kampung!");
		dstUser.combobox("setValue", "");
		return;
	}
	const user = dstUser.combobox("getValue");
	const req = await fetch(`${apiKapumg}/${user}/kampung`);
	const res = await req.json();
	tmpDstKampungList = [];
	if (res.length > 0) {
		for (const kampung of res) {
			Object.assign(kampung, { from: "dst" });
			tmpDstKampungList.push(kampung);
		}
	}
	pairKampung();
}

function pairKampung() {
	dstKampung.empty();
	for (const kampung of tmpSrcKampungList) {
		if (tmpDstKampungList.includes(kampung)) continue;
		tmpDstKampungList.push(kampung);
	}

	for (const kampung of tmpDstKampungList) {
		appendLi(kampung);
	}

	console.log(tmpDstKampungList);
}

function appendLi(kampung) {
	const li = document.createElement("li");
	li.appendChild(document.createTextNode(kampung.kampung));
	if (kampung.from === "src") li.style.color = "green";
	dstKampung.append(li);
}
