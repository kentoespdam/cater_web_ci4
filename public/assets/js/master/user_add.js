const addBtn = $("#add");
const win = $("#win");
const ff = $("#ff");
const userTxt = $("#user");
const namaTxt = $("#nama");
const emailTxt = $("#email");
const cabangOpt = $("#cabang");
const submitBt = $("#submit");
const resetBt = $("#reset");

win.window({
	width: 400,
	height: 250,
	minimizable: false,
	maximizable: false,
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
emailTxt.textbox({
	label: "Email",
	required: true,
	width: 300,
	validType: "email",
});

cabangOpt.combobox({
	label: "Cabang",
	prompt: "Cabang",
	width: "250",
});

submitBt.linkbutton({
	iconCls: "icon-save",
	onClick: submitForm,
});

resetBt.linkbutton({
	iconCls: "icon-cancel",
	onClick: resetForm,
});

async function submitForm() {
	$("#ff").form("submit", {
		url: `${baseUri}/api/master/user`,
		onSubmit: function () {
			$.messager.progress();
			const isValid = $(this).form("enableValidation").form("validate");
			return isValid;
		},
		success: (data) => {
			$.messager.progress("close");
			const response = JSON.parse(data);
			if (response.status === 400) {
				const messages = Object.entries(response.messages);
				messages.forEach(([key, value], i) => {
					$.messager.show({
						title: "Error",
						msg: value,
					});
				});
			}
			win.window("close");
			dg.datagrid("reload");
		},
	});
}

function resetForm() {
	win.window("close");
	ff.form("clear");
}
