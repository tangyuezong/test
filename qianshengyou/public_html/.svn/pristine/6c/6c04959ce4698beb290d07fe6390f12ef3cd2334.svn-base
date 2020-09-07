define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'users/deposit/index',
                    add_url: 'users/deposit/add',
//                    edit_url: 'users/deposit/edit',
//                    del_url: 'users/deposit/del',
                    multi_url: 'users/deposit/multi',
                    table: 'trans',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'status desc, refund_time desc, id desc',
//                sortOrder:'desc',
                columns: [
                    [
//                        {checkbox: true},
                        {field: 'operate', title: __('Operate'), table: table, buttons: [
							{
								name: 'refund', 
								text: '退押金', 
								title: '退押金', 
								icon: 'fa fa-hand-o-right', 
								classname: 'btn btn-xs btn-primary btn-ajax', 
								hidden: function(row) {
									return (row.status==1 && row.users.deposit>0) ? false : true;
								},
	                        	confirm:"确认退押金？", 
								url: '/admin/users/deposit/refund',
								success:function(data, ret) {
	                        		// 成功则刷新列表
	                        		table.bootstrapTable('refresh');
	                        	},
							},
						], events: Table.api.events.operate, formatter: Table.api.formatter.operate},
                        {field: 'id', title: __('Id')},
                        {field: 'uid', title: __('Uid')},
                        {field: 'users.phone', title: __('Users.phone')},
                        {field: 'users.deposit', title: __('Users.deposit'), operate:'BETWEEN'},
//                        {field: 'payment', title: __('Payment')},
//                        {field: 'item', title: __('Item')},
//                        {field: 'extra', title: __('Extra'), operate:'BETWEEN'},
//                        {field: 'order_no', title: __('Order_no')},
                        {field: 'tran_id', title: __('Tran_id')},
                        {field: 'tran_time', title: __('Tran_time')},
                        {field: 'amount', title: __('Amount'), operate:'BETWEEN'},
                        {field: 'refund_id', title: __('Refund_id')},
                        {field: 'refund_fee', title: __('Refund_fee'), operate:'BETWEEN'},
                        {field: 'refund_time', title: __('Refund_time'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), visible:false, searchList: {"1":__('未退押金'), "0":__('已退押金')}, sortable: true},
						{field: 'status_text', title: __('Status'), operate:false},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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
        refund: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});