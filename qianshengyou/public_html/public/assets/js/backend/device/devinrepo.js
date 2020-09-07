define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'jstree'], function ($, undefined, Backend, Table, Form, undefined) {
	//读取选中的条目
    $.jstree.core.prototype.get_all_checked = function (full) {
        var obj = this.get_selected(), i, j;
        for (i = 0, j = obj.length; i < j; i++) {
            obj = obj.concat(this.get_node(obj[i]).parents);
        }
        obj = $.grep(obj, function (v, i, a) {
            return v != '#';
        });
        obj = obj.filter(function (itm, i, a) {
            return i == a.indexOf(itm);
        });
        return full ? $.map(obj, $.proxy(function (i) {
            return this.get_node(i);
        }, this)) : obj;
    };
    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'device/devinrepo/index',
                    add_url: 'device/devinrepo/add',
                    edit_url: 'device/devinrepo/edit',
                    del_url: 'device/devinrepo/del',
//                    multi_url: 'device/devinrepo/multi',
                    table: 'device',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'dev_id', title: __('Dev_id')},
//                        {field: 'type', title: __('Type'), operate:false, formatter: function(type){
//                        	return devtype;
//                        	if(devtype==0) {
//                        		return "蓝牙锁";
//                        	} else {
//                        		var typedesc = "";
//                            	var devtype = parseInt(devtype);
//                            	if(devtype&2) typedesc += "NB";
//                            	if(devtype&1) typedesc += "蓝牙";
//                            	return typedesc + "锁";
//                        	}
//                        }},
                        {field: 'iscabinet', title: __('Iscabinet'), visible:false, searchList: {"1":__('储物柜'), "2":__('陪护床')}},
						{field: 'iscabinet_text', title: __('Iscabinet'), operate:false},
                        {field: 'devtype.name', title: __('Devtype.name'), operate:'LIKE'},
                        {field: 'mac', title: __('Mac')},
                        {field: 'blekey', title: __('Blekey')},
                        {field: 'blepwd', title: __('Blepwd')},
                        {field: 'netpoint_id', title: __('Netpoint_id'), operate:false},
                        {field: 'netpoint.name', title: __('Netpoint.name'), operate:'LIKE'},
                        {field: 'department', title: __('Department'), operate:'LIKE'},
                        {field: 'room', title: __('Room'), operate:'LIKE'},
                        {field: 'bed', title: __('Bed'), operate:'LIKE'},
//                        {field: 'mac2', title: __('Mac2')},
//                        {field: 'blekey2', title: __('Blekey2')},
//                        {field: 'blepwd2', title: __('Blepwd2')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
//                        {field: 'status', title: __('Status'), visible:false, searchList: {"1":__('Normal'), "0":__('Abnormal')}},
//						{field: 'status_text', title: __('Status'), operate:false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        multionline: function() {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
//                Form.api.bindevent($("form[role=form]"));
                Form.api.bindevent($("form[role=form]"), null, null, function () {
                    if ($("#treeview").size() > 0) {
                        var r = $("#treeview").jstree("get_all_checked");
                        $("input[name='row[ids]']").val(r.join(','));
                    }
                    return true;
                });
                if($("form[role=form]").attr("id")=="multionline-form") {
                	//渲染权限节点树
                    $.ajax({
                        url: "device/devinrepo/roletree",
                        type: 'post',
                        dataType: 'json',
//                        data: {id: id, pid: $(this).val()},
                        success: function (ret) {
                            if (ret.hasOwnProperty("code")) {
                                var data = ret.hasOwnProperty("data") && ret.data != "" ? ret.data : "";
                                if (ret.code === 1) {
                                    //销毁已有的节点树
                                    $("#treeview").jstree("destroy");
                                    Controller.api.rendertree(data);
                                } else {
                                    Backend.api.toastr.error(ret.msg);
                                }
                            }
                        }, error: function (e) {
                            Backend.api.toastr.error(e.message);
                        }
                    });
                }
                //全选和展开
                $(document).on("click", "#checkall", function () {
                    $("#treeview").jstree($(this).prop("checked") ? "check_all" : "uncheck_all");
                });
                $(document).on("click", "#expandall", function () {
                    $("#treeview").jstree($(this).prop("checked") ? "open_all" : "close_all");
                });
            },
            rendertree: function (content) {
                $("#treeview")
                        .on('redraw.jstree', function (e) {
                            $(".layer-footer").attr("domrefresh", Math.random());
                        })
                        .jstree({
                            "themes": {"stripes": true},
                            "checkbox": {
                                "keep_selected_style": false,
                            },
                            "types": {
                                "root": {
                                    "icon": "fa fa-folder-open",
                                },
                                "menu": {
                                    "icon": "fa fa-folder-open",
                                },
                                "file": {
                                    "icon": "fa fa-file-o",
                                }
                            },
                            "plugins": ["checkbox", "types"],
                            "core": {
                                'check_callback': true,
                                "data": content
                            }
                        });
            }
        }
    };
    return Controller;
});