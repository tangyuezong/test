define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/cabinetorder/index',
                    add_url: 'order/cabinetorder/add',
//                    edit_url: 'order/cabinetorder/edit',
//                    del_url: 'order/cabinetorder/del',
                    multi_url: 'order/cabinetorder/multi',
                    table: 'cabinetorder',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'order_no',
                sortName: 'step',
                sortOrder: 'asc',
                search: false,
                columns: [
                    [
//                        {checkbox: true},
                        {field: 'operate', title: __('Operate'), table: table, buttons: [
   							{
								name: 'rtncabinet', 
								text: '已和用户确认取走所有物品，后台操作还柜', 
								title: '已和用户确认取走所有物品，后台操作还柜', 
								hidden: function(row) {
									return (row.status==1 && row.step==1500) ? false : true;
								},
	                        	confirm:"确认已和用户确认取走 储物柜 所有物品, 并进行后台还柜？", 
								icon: 'fa fa-hand-o-right', 
								classname: 'btn btn-xs btn-primary btn-ajax', 
								url: function(row) {
									return '/admin/order/cabinetorder/rtncabinet/order_no/' + row.order_no;
								},
	                        	success:function(data, ret) {
	                        		// 成功则刷新列表
	                        		table.bootstrapTable('refresh');
	                        	},
//								callback: function(data) {
//									// 成功则刷新列表
//									table.bootstrapTable('refresh', {});
//								}, 
							}
						], events: Table.api.events.operate, formatter: Table.api.formatter.operate},
                        {field: 'order_no', title: __('Order_no')},
                        {field: 'users_id', title: __('Users_id'), sortable:true},
                        {field: 'phone', title: __('Phone')},
                        {field: 'dev_id', title: __('Dev_id'), sortable:true},
//                        {field: 'admin_id', title: __('Admin_id')},
                        {field: 'entity_id', title: __('Entity_id'), operate:false},
//                        {field: 'entity.name', title: __('Entity.name'), operate:'LIKE'},
                        {field: 'netpoint_id', title: __('Netpoint_id'), operate:false},
                        {field: 'netpoint.shortname', title: __('Netpoint.shortname'), operate:'LIKE'},
                        {field: 'device.department', title: __('Device.department'), operate:'LIKE'},
                        {field: 'device.room', title: __('Device.room'), operate:'LIKE'},
                        {field: 'device.bed', title: __('Device.bed'), operate:'LIKE'},
                        {field: 'step', title: __('Step'), sortable:true, visible:false, searchList: {"1500":__('Submitted'), "9000":__('Finished')}},
						{field: 'step_text', title: __('Step'), operate:false},
						{field: 'status', title: __('Status'), sortable:true, visible:false, searchList: {"1":__('Normal'), "0":__('Invalid'), "-1":__('Cancel')}},
						{field: 'status_text', title: __('Status'), operate:false},
                        {field: 'rtn_admin_id', title: __('Rtn_admin_id')},
                        {field: 'createtime', title: __('Createtime'), sortable:true, operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'endtime', title: __('Endtime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
//                        {field: 'users.nickname', title: __('Users.nickname'), operate:false},
                        {field: 'netpoint.name', title: __('Netpoint.name'), operate:false},
                        {field: 'device.mac', title: __('Device.mac'), operate:false}
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
        rtnbed: function () {
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