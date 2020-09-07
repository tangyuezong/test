define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/bedorder/index',
//                    add_url: 'order/bedorder/add',
                    edit_url: 'order/bedorder/edit',
//                    del_url: 'order/bedorder/del',
//                    multi_url: 'order/bedorder/multi',
                    table: 'bedorder',
                }
            });

            var table = $("#table");
            
          //当表格数据加载完成时
//            table.on('load-success.bs.table', function (e, data) {
//                //这里可以获取从服务端获取的JSON数据
//                //console.log(data);
//                //这里我们手动设置底部的值
//                $("#totalorder").text(data.extend.totalorder);
//                $("#totalorderamount").text(data.extend.totalorderamount);
//            });

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'order_no',
                sortName: 'order_no',
                search: false,
                columns: [
                    [
//                        {checkbox: true},
                        {field: 'operate', title: __('Operate'), table: table, buttons: [
  							{
								name: 'rtnbed', 
								text: '后台操作还床', 
								title: '后台操作还床', 
								hidden: function(row) {
									return (row.status==1 && row.step==1500) ? false : true;
								},
								icon: 'fa fa-hand-o-right', 
								classname: 'btn btn-xs btn-primary btn-dialog', 
								url: function(row) {
									return '/admin/order/bedorder/rtnbed/order_no/' + row.order_no;
								},
								callback: function(data) {
									// 成功则刷新列表
									table.bootstrapTable('refresh', {});
								}, 
							}
						], events: Table.api.events.operate, formatter: Table.api.formatter.operate},
                        {field: 'order_no', title: __('Order_no'), operate:'LIKE'},
                        {field: 'users_id', title: __('Users_id'), sortable:true, operate:false},
                        {field: 'phone', title: __('Phone'), operate:'LIKE'},
//                        {field: 'users.nickname', title: __('Users.nickname'), operate:false},
                        {field: 'dev_id', title: __('Dev_id'), sortable:true},
                        {field: 'netpoint.name', title: __('Netpoint.name'), operate:'LIKE', placeholder: '支持关键字模糊查询'},
                        {field: 'device.department', title: __('Device.department'), operate:'LIKE', placeholder: '支持关键字模糊查询'},
                        {field: 'device.room', title: __('Device.room'), operate:'LIKE', placeholder: '支持关键字模糊查询'},
                        {field: 'device.bed', title: __('Device.bed'), operate:'LIKE', placeholder: '支持关键字模糊查询'},
                        {field: 'admin_id', title: __('Admin_id')},
                        {field: 'entity_id', title: __('Entity_id'), operate:false},
                        {field: 'netpoint_id', title: __('Netpoint_id'), sortable:true, operate:false},
                        {field: 'price_id', title: __('Price_id'), operate:false},
//                        {field: 'deposit', title: __('Deposit'), operate:'BETWEEN'},
                        {field: 'deposit', title: __('Deposit'), operate:false},
//                        {field: 'lightnight', title: __('Lightnight'), operate:false},
                        {field: 'lightnight', title: __('Lightnight'), visible:false, searchList: {"1":__('日间'), "2":__('夜间')}},
						{field: 'lightnight_text', title: __('Lightnight'), operate:false},
                        {field: 'canusestart', title: __('Canusestart'), sortable:true, operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'canuseend', title: __('Canuseend'), sortable:true, operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
//                        {field: 'pricetype', title: __('Pricetype')},
                        {field: 'pricetype', title: __('Pricetype'), visible:false, searchList: {"1":__('Hourtype'), "2":__('Timestype'), "3":__('Daytype')}},
						{field: 'pricetype_text', title: __('Pricetype'), operate:false},
                        {field: 'hourprice', title: __('Hourprice'), operate:false},
                        {field: 'lighttimesprice', title: __('Lighttimesprice'), operate:false},
                        {field: 'nighttimesprice', title: __('Nighttimesprice'), operate:false},
                        {field: 'dayprice', title: __('Dayprice'), operate:false},
                        {field: 'days', title: __('Days'), operate:false},
                        {field: 'pricedata', title: __('Pricedata'), operate:false},
                        {field: 'total', title: __('Total'), operate:false},
                        {field: 'save', title: __('Save'), operate:false},
                        {field: 'pay', title: __('Pay'), operate:'BETWEEN'},
                        {field: 'tran_id', title: __('Tran_id')},
                        {field: 'refund', title: __('Refund'), operate:false},
//                        {field: 'refundtime', title: __('Refundtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'refundtime', title: __('Refundtime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'refundnote', title: __('Refundnote'), operate:false},
//                        {field: 'step', title: __('Step')},
//                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.status},
						{field: 'step', title: __('Step'), visible:false, sortable:true, searchList: {"1500":__('Paid'), "9000":__('Finished')}},
						{field: 'step_text', title: __('Step'), operate:false},
						{field: 'status', title: __('Status'), visible:false, searchList: {"1":__('Normal'), "0":__('Invalid'), "-1":__('Cancel')}},
						{field: 'status_text', title: __('Status'), operate:false},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', sortable:true, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'endtime', title: __('Endtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
//                        {field: 'netpoint.shortname', title: __('Netpoint.shortname'), operate:'LIKE'},
//                        {field: 'entity.name', title: __('Entity.name'), operate:false},
                        {field: 'device.mac', title: __('Device.mac'), operate:false},
//                        {field: 'trans.payment', title: __('Trans.payment')},
//                        {field: 'trans.item', title: __('Trans.item')},
//                        {field: 'trans.tran_id', title: __('Trans.tran_id'), operate:false},
//                        {field: 'trans.tran_time', title: __('Trans.tran_time'), operate:false},
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