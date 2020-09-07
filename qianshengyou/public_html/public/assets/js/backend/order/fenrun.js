define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/fenrun/index',
                    add_url: 'order/fenrun/add',
//                    edit_url: 'order/fenrun/edit',
//                    del_url: 'order/fenrun/del',
                    multi_url: 'order/fenrun/multi',
                    table: 'fenrun',
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
//                        {checkbox: true},
//                        {field: 'id', title: __('Id')},
                        {field: 'operate', title: __('Operate'), table: table, buttons: [
 							{
								name: 'fenrunpay', 
								text: '分润转账', 
								title: '分润转账', 
								hidden: function(row) {
									return (row.status===0 && row.accountset.openid) ? false : true;
								},
								icon: 'fa fa-hand-o-right', 
								classname: 'btn btn-xs btn-primary btn-dialog', 
								url: function(row) {
									return '/admin/order/fenrun/fenrunpay/id/' + row.id + '/name/' + row.accountset.name + '/openid/' + row.accountset.openid;
								},
								callback: function(data) {
									// 成功则刷新列表
									table.bootstrapTable('refresh', {});
								}, 
							}
						], events: Table.api.events.operate, formatter: Table.api.formatter.operate},
                        {field: 'group_id', title: __('Group_id'), operate: false},
                        {field: 'accountset.name', title: __('Accountset.name'), operate: 'LIKE'},
                        {field: 'accountset.phone', title: __('Accountset.phone'), operate: 'LIKE'},
                        {field: 'accountset.openid', title: __('Accountset.openid'), operate: false, formatter: function(openid) {
                        	return openid ? openid : '未设置分润账号';
                        }},
                        {field: 'accountset.realname', title: __('Accountset.realname'), operate: 'LIKE'},
                        {field: 'fenrundate', title: __('Fenrundate'), formatter: function(fenrundate) {
                        	var datestr = fenrundate.toString();
                        	return datestr.substr(0,4) + "-" + datestr.substr(4,2) + "-" + datestr.substr(6,2);
                        }},
                        {field: 'amount', title: __('Amount'), operate:'BETWEEN'},
                        {field: 'realamount', title: __('Realamount'), operate:'BETWEEN'},
                        {field: 'tran_id', title: __('Tran_id'), operate:false},
                        {field: 'tran_time', title: __('Tran_time'), operate:false},
                        {field: 'paynote', title: __('Paynote'), operate:false},
                        {field: 'admin_id', title: __('Admin_id'), operate:false},
                        {field: 'createtime', title: __('Createtime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), visible:false, searchList: {"1":__('已分润'), "0":__('未分润'), "-1":__('分润调用微信付款接口返回错误')}},
						{field: 'status_text', title: __('Status'), operate:false},
//                        {field: 'accountset.bank_name', title: __('Accountset.bank_name')},
//                        {field: 'accountset.bank_no', title: __('Accountset.bank_no')},
//                        {field: 'accountset.true_name', title: __('Accountset.true_name')},
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
        fenrunmultipay: function () {
            Controller.api.bindevent();
        },
        fenrunpay: function () {
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