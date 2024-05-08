const dg = $("#dg");
const apiUri = `${baseUri}/api/master/user`;

dg.datagrid({
	title: "Master User",
	url: apiUri,
	method: "GET",
	columns: [
		[
			{ field: "user", title: "User", width: "100" },
			{ field: "nama", title: "Nama Lengkap", width: "180" },
			{ field: "cabang", title: "Cabang", width: "150" },
			{
				title: "Aksi",
				field: "aksi",
				formatter: (value, row) => {
					const editBtn = `<a class="editBtn" href="#" onClick="edit('${row.user}')">Edit</a>`;
					const delBtn = `<a class="delBtn" href="#" onClick="del('${row.user}')")>Delete</a>`;
					return `${editBtn} ${delBtn}`;
				},
				align: "center",
				width: 180,
			},
		],
	],
	rownumbers: true,
	pagination: true,
	resizable: true,
	onLoadSuccess: (data) => {
		setTimeout(() => {
			$(".editBtn").linkbutton({ iconCls: "icon-edit" });
			$(".delBtn").linkbutton({ iconCls: "icon-clear" });
		}, 500);
	},
	view: detailview,
	detailFormatter: (index, row) => {
		return '<div class="ddv" style="padding:5px 0"></div>';
	},
	onExpandRow: (index, row) => {
		const ddv = dg.datagrid("getRowDetail", index).find("div.ddv");
		ddv.datagrid({
			method: "GET",
			url: `${apiUri}/${row.user}/kampung`,
			fitColumns: true,
			singleSelect: true,
			columns: [
				[
					{ field: "id", title: "Kode", width: "100" },
					{ field: "kampung", title: "Nama", width: "150" },
				],
			],
			onResize: () => {
				dg.datagrid("fixDetailRowHeight", index);
			},
			onLoadSuccess: () => {
				setTimeout(() => {
					dg.datagrid("fixDetailRowHeight", index);
				}, 100);
			},
		});
		dg.datagrid("fixDetailRowHeight", index);
	},
});

function edit(user) {
	console.log(`edit ${user}`);
}

function del(user) {
	console.log(`delete ${user}`);
}
