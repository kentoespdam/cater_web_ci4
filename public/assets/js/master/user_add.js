const addBtn = $("#add");
const win = $("#win");
const userTxt = $("#user");
const namaTxt = $("#nama");
const cabangOpt = $("#cabang");
const submitBt = $("#submit");
const resetBt = $("#reset");

win.window({
	width: 400,
	height: 300,
	modal: false,
});

addBtn.linkbutton({
	iconCls: "icon-add",
	onClick: () => {
		win.window("open");
	},
});

win.window("close");

userTxt.textbox({
	label: "Username",
	required: true,
	width: 250,
});

namaTxt.textbox({
	label: "Nama",
	required: true,
	width: 300,
});

cabangOpt.combobox({
	label: "Cabang",
	prompt: "Cabang",
	width: "250",
});

submitBt.linkbutton({
	iconCls: "icon-save",
});

resetBt.linkbutton({
	iconCls: "icon-cancel",
});
