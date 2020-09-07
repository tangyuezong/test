define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'device/issue/index',
                    add_url: 'device/issue/add',
                    edit_url: 'device/issue/edit',
//                    del_url: 'device/issue/del',
                    multi_url: 'device/issue/multi',
                    table: 'issue',
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
//                        {field: 'id', title: __('Id'), operate:false},
//                        {field: 'users_id', title: __('Users_id'), operate:false},
                        {field: 'device_id', title: __('Device_id')},
//                        {field: 'netpoint_id', title: __('Netpoint_id'), operate:false},
//                        {field: 'entity_id', title: __('Entity_id'), operate:false}
                        {field: 'title', title: __('Title'), operate:'LIKE'},
                        {field: 'issue_images', title: __('Issue_images'), formatter: Table.api.formatter.images, operate:false},
                        {field: 'issue_desc', title: __('Issue_desc'), operate:'LIKE'},
                        {field: 'fixdesc', title: __('Fixdesc'), operate:'LIKE'},
                        {field: 'status', title: __('Status'), visible:false, searchList: {"0":__('Pending'), "1":__('Processing'), "9":__('Finish')}},
						{field: 'status_text', title: __('Status'), operate:false},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'netpoint.name', title: __('Netpoint.name'), operate:'LIKE'},
//                        {field: 'netpoint.shortname', title: __('Netpoint.shortname'), operate:'LIKE'},
                        {field: 'netpoint.netaddr', title: __('Netpoint.netaddr'), operate:'LIKE'},
//                        {field: 'netpoint.netlng', title: __('Netpoint.netlng'), operate:'BETWEEN'},
//                        {field: 'netpoint.netlat', title: __('Netpoint.netlat'), operate:'BETWEEN'},
                        {field: 'entity.name', title: __('Entity.name'), operate:'LIKE'},
//                        {field: 'entity.entity_image', title: __('Entity.entity_image'), formatter: Table.api.formatter.image, operate:false},
                        {field: 'users.phone', title: __('Users.phone'), operate:'LIKE'},
                        {field: 'users.nickname', title: __('Users.nickname'), operate:false},
                        {field: 'admin.name', title: __('Admin.name')},
//                        {field: 'users.wxgender', title: __('Users.wxgender'), operate:false},
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