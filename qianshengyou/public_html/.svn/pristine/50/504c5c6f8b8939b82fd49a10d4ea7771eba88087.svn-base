define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'auth/accountset/index',
                    add_url: 'auth/accountset/add',
//                    edit_url: 'auth/accountset/edit',
//                    del_url: 'auth/accountset/del',
                    multi_url: 'auth/accountset/multi',
                    table: 'auth_group',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search: false,
                export: false,
                commonSearch: false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name')},
                        {field: 'phone', title: __('Phone')},
                        {field: 'openid', title: __('Openid')},
                        {field: 'realname', title: __('Realname')},
//                        {field: 'bank_name', title: __('Bank_name')},
//                        {field: 'bank_no', title: __('Bank_no')},
//                        {field: 'true_name', title: __('True_name')},
                        {field: 'operate', title: __('Operate'), table: table, buttons: [
  							{
 								name: 'setaccount', 
 								text: '设置微信零钱账号', 
 								title: '设置微信零钱账号', 
 								icon: 'fa fa-hand-o-right', 
 								classname: 'btn btn-xs btn-primary btn-dialog', 
 								url: '/admin/auth/accountset/setaccount/',
 								callback: function(data) {
 									// 成功则刷新列表
 									table.bootstrapTable('refresh', {});
 								}, 
 							}
 						], events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
        setaccount: function() {
        	Controller.api.bindevent();
            
            $("#c-phone").change(function() {
            	if($("#c-phone").val()!="") {
            		if($("#c-phone").val().length!=11) {
            			$("#c-openid").val("");
                        Backend.api.toastr.info("手机号长度11位");
            			return false;
            		}
            		$.ajax({
                        url: "auth/accountset/getopenid",
                        type: 'post',
                        dataType: 'json',
                        data: {
                        	phone: $("#c-phone").val()
                        },
                        async: false,
                        success: function (ret) {
                        	if(ret.code==1) {
                        		$("#c-openid").val(ret.data);
                        	} else {
                        		$("#c-openid").val("");
                                Backend.api.toastr.error(ret.msg);
                        	}
                        },
                        error: function(e) {
                    		$("#c-openid").val("");
                            Backend.api.toastr.error(e.message);
                        }
                    })
            	} else {
            		$("#c-openid").val("");
            	}
            });
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});