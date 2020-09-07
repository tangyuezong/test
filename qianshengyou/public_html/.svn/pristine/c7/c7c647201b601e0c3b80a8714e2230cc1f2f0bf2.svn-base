define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'device/price/index',
                    add_url: 'device/price/add',
                    edit_url: 'device/price/edit',
//                    del_url: 'device/price/del',
//                    multi_url: 'device/price/multi',
                    table: 'price',
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
                        {field: 'name', title: __('Name')},
                        {field: 'lightstart', title: __('Lightstart')},
                        {field: 'lightend', title: __('Lightend')},
                        {field: 'nightstart', title: __('Nightstart')},
                        {field: 'nightend', title: __('Nightend')},
                        {field: 'hourdeposit', title: __('Hourdeposit'), operate:'BETWEEN'},
                        {field: 'hourprice', title: __('Hourprice'), operate:'BETWEEN'},
                        {field: 'timesdeposit', title: __('Timesdeposit'), operate:'BETWEEN'},
                        {field: 'lighttimesprice', title: __('Lighttimesprice'), operate:'BETWEEN'},
                        {field: 'nighttimesprice', title: __('Nighttimesprice'), operate:'BETWEEN'},
                        {field: 'daydeposit', title: __('Daydeposit'), operate:'BETWEEN'},
                        {field: 'dayprice', title: __('Dayprice'), operate:'BETWEEN'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), visible:false, searchList: {"1":__('Normal'), "0":__('Abnormal')}},
						{field: 'status_text', title: __('Status'), operate:false},
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
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});