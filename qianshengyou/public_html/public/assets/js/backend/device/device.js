define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'device/device/index',
                    add_url: 'device/device/add',
                    edit_url: 'device/device/edit',
                    del_url: 'device/device/del',
//                    multi_url: 'device/device/multi',
                    table: 'device',
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
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate},
                        {field: 'id', title: __('Id'), operate:false},
                        {field: 'dev_id', title: __('Dev_id'), sortable:true},
                        {field: 'iscabinet', title: __('Iscabinet'), visible:false, searchList: {"1":__('储物柜'), "2":__('陪护床')}},
						{field: 'iscabinet_text', title: __('Iscabinet'), operate:false},
                        {field: 'price_id', title: __('Price_id'), operate:false},
                        {field: 'entity_id', title: __('Entity_id'), operate:false},
                        {field: 'admin_id', title: __('Admin_id'), operate:false},
                        {field: 'netpoint_id', title: __('Netpoint_id'), operate:false},
                        {field: 'netpoint.name', title: __('Netpoint.name'), operate:'LIKE'},
                        {field: 'department', title: __('Department'), operate:'LIKE'},
                        {field: 'room', title: __('Room'), operate:'LIKE'},
                        {field: 'bed', title: __('Bed'), operate:'LIKE'},
                        {field: 'devtype.name', title: __('Devtype.name'), operate:'LIKE'},
                        {field: 'mac', title: __('Mac'), operate:'LIKE'},
                        {field: 'blekey', title: __('Blekey'), operate:false},
                        {field: 'blepwd', title: __('Blepwd'), operate:false},
//                        {field: 'mac2', title: __('Mac2'), operate:'LIKE'},
//                        {field: 'blekey2', title: __('Blekey2'), operate:false},
//                        {field: 'blepwd2', title: __('Blepwd2'), operate:false},
                        {field: 'createtime', title: __('Createtime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:false, addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), visible:false, searchList: {"1":"上线", "-1":"下线"}},
						{field: 'status_text', title: __('Status'), operate:false},
                        {field: 'netpoint.admin_id', title: __('Netpoint.admin_id'), operate:false},
                        {field: 'entity.name', title: __('Entity.name'), operate:false},
                        {field: 'price.name', title: __('Price.name'), operate:'LIKE'},
                        {field: 'price.lightstart', title: __('Price.lightstart')},
                        {field: 'price.lightend', title: __('Price.lightend')},
                        {field: 'price.nightstart', title: __('Price.nightstart')},
                        {field: 'price.nightend', title: __('Price.nightend')},
                        {field: 'price.hourdeposit', title: __('Price.hourdeposit'), operate:false},
                        {field: 'price.hourprice', title: __('Price.hourprice'), operate:false},
                        {field: 'price.timesdeposit', title: __('Price.timesdeposit'), operate:false},
                        {field: 'price.lighttimesprice', title: __('Price.lighttimesprice'), operate:false},
                        {field: 'price.nighttimesprice', title: __('Price.nighttimesprice'), operate:false},
                        {field: 'price.daydeposit', title: __('Price.daydeposit'), operate:false},
                        {field: 'price.dayprice', title: __('Price.dayprice'), operate:false}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            
          //绑定TAB事件
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                // var options = table.bootstrapTable(tableOptions);
                var typeStr = $(this).attr("href").replace('#','');
                var options = table.bootstrapTable('getOptions');
                options.pageNumber = 1;
                options.queryParams = function (params) {
                    // params.filter = JSON.stringify({type: typeStr});
                    params.status = typeStr;

                    return params;
                };
                table.bootstrapTable('refresh', {});
                return false;

            });
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