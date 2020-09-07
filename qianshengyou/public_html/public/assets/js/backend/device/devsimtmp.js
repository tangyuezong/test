define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'device/devsimtmp/index',
//                    add_url: 'device/devsimtmp/add',
//                    edit_url: 'device/devsimtmp/edit',
//                    del_url: 'device/devsimtmp/del',
//                    multi_url: 'device/devsimtmp/multi',
                    table: 'devsim',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search: false,
                showToggle: false,
                showColumns: false,
                showExport: false,
                columns: [
                    [
//                        {checkbox: true},
//                        {field: 'id', title: __('Id')},
                        {field: 'mac', title: __('Mac'), operate: 'LIKE'},
                        {field: 'imei', title: __('Imei'), operate: 'LIKE'},
                        {field: 'imsi', title: __('Imsi'), operate: 'LIKE'},
                        {field: 'lockstat', title: __('Lockstat'), operate: 'LIKE'},
//                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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