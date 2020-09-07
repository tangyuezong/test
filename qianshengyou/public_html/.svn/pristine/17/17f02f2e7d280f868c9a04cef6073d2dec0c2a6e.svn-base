define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'device/lowpower/index',
                    add_url: 'device/lowpower/add',
                    edit_url: 'device/lowpower/edit',
//                    del_url: 'device/lowpower/del',
                    multi_url: 'device/lowpower/multi',
                    table: 'lowpower',
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
                        {field: 'device_id', title: __('Device_id')},
//                        {field: 'netpoint_id', title: __('Netpoint_id')},
//                        {field: 'entity_id', title: __('Entity_id')},
                        {field: 'power', title: __('Power')},
                        {field: 'fixdesc', title: __('Fixdesc')},
                        {field: 'status', title: __('Status'), visible:false, searchList: {"0":__('Pending'), "1":__('Processing'), "9":__('Finish')}},
						{field: 'status_text', title: __('Status'), operate:false},
//                        {field: 'admin_id', title: __('Admin_id')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'netpoint.name', title: __('Netpoint.name'), operate:'LIKE'},
//                        {field: 'netpoint.shortname', title: __('Netpoint.shortname'), operate:'LIKE'},
                        {field: 'netpoint.netaddr', title: __('Netpoint.netaddr'), operate:'LIKE'},
                        {field: 'netpoint.netlng', title: __('Netpoint.netlng'), operate:'BETWEEN'},
                        {field: 'netpoint.netlat', title: __('Netpoint.netlat'), operate:'BETWEEN'},
                        {field: 'entity.name', title: __('Entity.name')},
                        {field: 'admin.name', title: __('Admin.name')},
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