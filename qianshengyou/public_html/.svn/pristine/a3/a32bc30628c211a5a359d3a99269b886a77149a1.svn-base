define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'order/reporting/index',
//                    add_url: 'order/reporting/add',
//                    edit_url: 'order/reporting/edit',
//                    del_url: 'order/reporting/del',
//                    multi_url: 'order/reporting/multi',
                    table: '',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'order_no',
                sortName: 'order_no',
                search: false,
                pageSize: 1000,
                pageList: 'All',
                columns: [
                    [
//                        {checkbox: true},
//                        {field: 'order_no', title: __('Order_no')},
//                        {field: 'users_id', title: __('Users_id')},
//                        {field: 'phone', title: __('Phone')},
//                        {field: 'dev_id', title: __('Dev_id')},
//                        {field: 'admin_id', title: __('Admin_id')},
//                        {field: 'entity_id', title: __('Entity_id')},
                        {field: 'netpoint_id', title: __('Netpoint_id'), visible: false, searchList: $.getJSON("order/reporting/nepointsearch")},
//                        {field: 'pricetype', title: __('Pricetype'), visible: false},
//                        {field: 'price_id', title: __('Price_id')},
//                        {field: 'deposit', title: __('Deposit'), operate:'BETWEEN'},
//                        {field: 'lightnight', title: __('Lightnight'), visible: false},
//                        {field: 'canusestart', title: __('Canusestart')},
//                        {field: 'canuseend', title: __('Canuseend')},
//                        {field: 'hourprice', title: __('Hourprice'), operate:'BETWEEN'},
//                        {field: 'lighttimesprice', title: __('Lighttimesprice'), operate:'BETWEEN'},
//                        {field: 'nighttimesprice', title: __('Nighttimesprice'), operate:'BETWEEN'},
//                        {field: 'dayprice', title: __('Dayprice'), operate:'BETWEEN'},
//                        {field: 'days', title: __('Days')},
//                        {field: 'pricedata', title: __('Pricedata')},
//                        {field: 'total', title: __('Total'), operate:'BETWEEN'},
//                        {field: 'save', title: __('Save'), operate:'BETWEEN'},
//                        {field: 'pay', title: __('Pay'), operate:'BETWEEN'},
//                        {field: 'tran_id', title: __('Tran_id')},
//                        {field: 'refund', title: __('Refund'), operate:'BETWEEN'},
//                        {field: 'refundtime', title: __('Refundtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
//                        {field: 'refundnote', title: __('Refundnote')},
//                        {field: 'step', title: __('Step')},
//                        {field: 'status', title: __('Status')},
//                        {field: 'rtn_admin_id', title: __('Rtn_admin_id')},
//                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'endtime', title: __('Endtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime, visible: false},
//                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}

                      {field: 'netpoint_name', title: __('Netpoint_name'), operate: false},
                      {field: 'devicenums', title: __('Devicenums'), operate: false},
                      {field: 'totalorders', title: __('Totalorders'), operate: false},
                      {field: 'totalamount', title: __('Totalamount'), operate: false},
                      {field: 'userate', title: __('Userate'), operate: false},
                      {field: 'incomerate', title: __('Incomerate'), operate: false},
                      {field: 'freeorders', title: __('Freeorders'), operate: false},
                      {field: 'hourorders', title: __('Hourorders'), operate: false},
                      {field: 'timesorders', title: __('Timesorders'), operate: false},
                      {field: 'dayorders', title: __('Dayorders'), operate: false},
                      {field: 'houramount', title: __('Houramount'), operate: false},
                      {field: 'timesamount', title: __('Timesamount'), operate: false},
                      {field: 'dayamount', title: __('Dayamount'), operate: false},
                      {field: 'lightorders', title: __('Lightorders'), operate: false},
                      {field: 'nightorders', title: __('Nightorders'), operate: false},
                      {field: 'lightamount', title: __('Lightamount'), operate: false},
                      {field: 'nightamount', title: __('Nightamount'), operate: false},
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
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});