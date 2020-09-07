define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'users/user/index',
                    add_url: 'users/user/add',
                    edit_url: 'users/user/edit',
                    del_url: 'users/user/del',
                    multi_url: 'users/user/multi',
                    table: 'users',
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
                        {field: 'phone', title: __('Phone')},
                        {field: 'openid', title: __('Openid')},
                        {field: 'nickname', title: __('Nickname')},
                        {field: 'head_image', title: __('Head_image'), formatter: Table.api.formatter.image},
                        {field: 'wxgender', title: __('Wxgender')},
                        {field: 'brand', title: __('Brand')},
                        {field: 'model', title: __('Model')},
                        {field: 'city', title: __('City')},
                        {field: 'province', title: __('Province')},
                        {field: 'balance', title: __('Balance'), operate:'BETWEEN'},
                        {field: 'score', title: __('Score'), operate:'BETWEEN'},
                        {field: 'credit', title: __('Credit'), operate:'BETWEEN'},
                        {field: 'deposit', title: __('Deposit'), operate:'BETWEEN'},
                        {field: 'realname', title: __('Realname')},
                        {field: 'id_type', title: __('Id_type')},
                        {field: 'id_no', title: __('Id_no')},
                        {field: 'id_image', title: __('Id_image'), formatter: Table.api.formatter.image},
                        {field: 'realstat', title: __('Realstat')},
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