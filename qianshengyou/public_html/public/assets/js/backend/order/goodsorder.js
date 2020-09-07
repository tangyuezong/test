define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/goodsorder/index',
                    add_url: 'order/goodsorder/add',
//                    edit_url: 'order/goodsorder/edit',
//                    del_url: 'order/goodsorder/del',
                    multi_url: 'order/goodsorder/multi',
                    table: 'goodsorder',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'status,id',
                sortOrder:'asc,desc',
                columns: [
                    [
//                        {checkbox: true},
                        {field: 'operate', title: __('Operate'), table: table, buttons: [
   							{
								name: 'passgood', 
								text: '已派送商品', 
								title: '已派送商品', 
								icon: 'fa fa-hand-o-right', 
								classname: 'btn btn-xs btn-primary btn-ajax', 
								hidden: function(row) {
									return row.status==1 ? false : true;
								},
	                        	confirm:"确认已将商品派送给用户？", 
								url: '/admin/order/goodsorder/passgood',
								success:function(data, ret) {
	                        		// 成功则刷新列表
	                        		table.bootstrapTable('refresh');
	                        	},
//								url: function(row) {
//									return '/admin/order/goodsorder/passgood/order_no/' + row.order_no;
//								},
//								callback: function(data) {
//									// 成功则刷新列表
//									table.bootstrapTable('refresh', {});
//								}
							},
							{
								name: 'cancel', 
								text: '取消订单', 
								title: '取消订单', 
								hidden: function(row) {
									return row.status==1 ? false : true;
								},
								icon: 'fa fa-hand-o-right', 
								classname: 'btn btn-xs btn-primary btn-dialog', 
//								url: '/admin/order/goodsorder/cancel' + '/phone/' + ,
								url: function(row) {
									return '/admin/order/goodsorder/cancel/id/' + row.id + '/goodsname/' + row.goods.name + '/netpoint/' + row.netpoint.name + '/phone/' + row.users.phone;
								},
								callback: function(data) {
									// 成功则刷新列表
									table.bootstrapTable('refresh', {});
								}
							}
						], events: Table.api.events.operate, formatter: Table.api.formatter.operate},
//                        {field: 'id', title: __('Id')},
						{field: 'status', title: __('Status'), visible:false, searchList: {"1":__('Pending'), "9":__('Finish'), "99999":__('Cancel')}},
						{field: 'status_text', title: __('Status'), operate:false},
                        {field: 'order_no', title: __('Order_no'), operate:'LIKE'},
                        {field: 'users_id', title: __('Users_id'), operate:false},
                        {field: 'users.phone', title: __('Users.phone'), operate:'LIKE'},
                        {field: 'users.nickname', title: __('Users.nickname'), operate:'LIKE'},
//                        {field: 'goods_id', title: __('Goods_id'), operate:false},
                        {field: 'goods.name', title: __('Goods.name'), operate:'LIKE'},
                        {field: 'goods.goods_image', title: __('Goods.goods_image'), formatter: Table.api.formatter.image},
                        {field: 'netpoint.name', title: __('Netpoint.name'), operate:'LIKE'},
                        {field: 'department', title: __('Department'), operate:'LIKE'},
                        {field: 'room', title: __('Room'), operate:'LIKE'},
                        {field: 'price', title: __('Price'), operate:false},
                        {field: 'num', title: __('Num'), operate:false},
                        {field: 'total', title: __('Total'), operate:false},
                        {field: 'trans.tran_id', title: __('Trans.tran_id'), operate:'LIKE'},
                        {field: 'trans.tran_time', title: __('Trans.tran_time'), operate:false},
                        {field: 'refund', title: __('Refund'), operate:false},
                        {field: 'refundnote', title: __('Refundnote'), operate:false},
                        {field: 'refund_id', title: __('Refund_id'), operate:false},
                        {field: 'refund_time', title: __('Refund_time'), operate:false},
                        {field: 'createtime', title: __('Createtime'), sortable:true, operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), sortable:true, operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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
        passgood: function () {
            Controller.api.bindevent();
        },
        cancel: function () {
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