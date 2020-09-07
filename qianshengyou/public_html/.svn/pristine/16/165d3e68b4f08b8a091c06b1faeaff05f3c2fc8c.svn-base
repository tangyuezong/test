define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'device/netpoint/index',
                    add_url: 'device/netpoint/add',
                    edit_url: 'device/netpoint/edit',
//                    del_url: 'device/netpoint/del',
//                    multi_url: 'device/netpoint/multi',
                    table: 'netpoint',
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
                       {field: 'operate', title: __('Operate'), table: table, buttons: [
							{
								name: 'setpercent', 
								text: '设置分润比例', 
								title: '设置分润比例', 
								icon: 'fa fa-hand-o-right', 
								classname: 'btn btn-xs btn-primary btn-dialog', 
								url: '/admin/device/netpoint/setpercent',
								callback: function(data) {
									// 成功则刷新列表
									table.bootstrapTable('refresh', {});
								}, 
							},
							{
								name: 'setmaintain', 
								text: '设置维护人员', 
								title: '设置维护人员', 
								icon: 'fa fa-hand-o-right', 
								classname: 'btn btn-xs btn-primary btn-dialog', 
								url: '/admin/device/netpoint/setmaintain',
								callback: function(data) {
									// 成功则刷新列表
									table.bootstrapTable('refresh', {});
								}, 
							},
							{
								name: 'setmall', 
								text: '设置商城维护员', 
								title: '设置商城维护员', 
								icon: 'fa fa-hand-o-right', 
								classname: 'btn btn-xs btn-primary btn-dialog', 
								url: '/admin/device/netpoint/setmall',
								callback: function(data) {
									// 成功则刷新列表
									table.bootstrapTable('refresh', {});
								}, 
							}
						], events: Table.api.events.operate, formatter: Table.api.formatter.operate},
                        {field: 'id', title: __('Id')},
                        {field: 'admin_id', title: __('Admin_id')},
                        {field: 'name', title: __('Name')},
                        {field: 'shortname', title: __('Shortname')},
                        {field: 'netaddr', title: __('Netaddr')},
                        {field: 'netlng', title: __('Netlng'), operate:'BETWEEN'},
                        {field: 'netlat', title: __('Netlat'), operate:'BETWEEN'},
//                        {field: 'deposit', title: __('Deposit')},
                        {field: 'mtadmin.nickname', title: __('Mtadmin.nickname')},
                        {field: 'malladmin.nickname', title: __('Malladmin.nickname')},
                        {field: 'level1', title: __('Level1'), operate:false, formatter: function (level1) {
                        	if(level1>0) {
                        		var rtnval = level1;
                            	$.ajax({
                                    url: "device/netpoint/groupinfo",
                                    type: 'post',
                                    dataType: 'json',
                                    data: {id: level1},
                                    async: false,
                                    success: function (ret) {
                                    	if(ret.code==1) {
                                    		if(ret.data.length>0) {
                                    			rtnval = ret.data[0].name;
                                    		}
                                    	}
                                    },
                                })
                                return rtnval;
                        	}
                        }},
                        {field: 'level2', title: __('Level2'), operate:false, formatter: function (level2) {
                        	if(level2>0) {
                        		var rtnval = level2;
                            	$.ajax({
                                    url: "device/netpoint/groupinfo",
                                    type: 'post',
                                    dataType: 'json',
                                    data: {id: level2},
                                    async: false,
                                    success: function (ret) {
                                    	if(ret.code==1) {
                                    		if(ret.data.length>0) {
                                    			rtnval = ret.data[0].name;
                                    		}
                                    	}
                                    },
                                })
                                return rtnval;
                        	}
                        }},
                        {field: 'level3', title: __('Level3'), operate:false, formatter: function (level3) {
                        	if(level3>0) {
                        		var rtnval = level3;
                        		$.ajax({
                                    url: "device/netpoint/groupinfo",
                                    type: 'post',
                                    dataType: 'json',
                                    data: {id: level3},
                                    async: false,
                                    success: function (ret) {
                                    	if(ret.code==1) {
                                    		if(ret.data.length>0) {
                                    			rtnval = ret.data[0].name;
                                    		}
                                    	}
                                    },
                                })
                        	}
                            return rtnval;
                        }},
                        {field: 'level4', title: __('Level4'), operate:false, formatter: function (level4) {
                        	if(level4>0) {
                        		var rtnval = level4;
                        		$.ajax({
                                    url: "device/netpoint/groupinfo",
                                    type: 'post',
                                    dataType: 'json',
                                    data: {id: level4},
                                    async: false,
                                    success: function (ret) {
                                    	if(ret.code==1) {
                                    		if(ret.data.length>0) {
                                    			rtnval = ret.data[0].name;
                                    		}
                                    	}
                                    },
                                })
                        	}
                            return rtnval;
                        }},
                        {field: 'percent1', title: __('Percent1'), operate:false, formatter: function (percent1) {
                        	if(percent1>0) {
                        		return percent1;
                        	}
                        }},
                        {field: 'percent2', title: __('Percent2'), operate:false, formatter: function (percent2) {
                        	if(percent2>0) {
                        		return percent2;
                        	}
                        }},
                        {field: 'percent3', title: __('Percent3'), operate:false, formatter: function (percent3) {
                        	if(percent3>0) {
                        		return percent3;
                        	}
                        }},
                        {field: 'percent4', title: __('Percent4'), operate:false, formatter: function (percent4) {
                        	if(percent4>0) {
                        		return percent4;
                        	}
                        }},
                        {field: 'status', title: __('Status'), visible:false, searchList: {"1":__('Normal'), "0":__('Abnormal')}},
						{field: 'status_text', title: __('Status'), operate:false},
                        {field: 'admin.username', title: __('Admin.username')},
                        {field: 'admin.nickname', title: __('Admin.nickname')},
                        {field: 'admin.email', title: __('Admin.email')}
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
        setpercent: function() {
            Controller.api.bindevent();
            
//            Form.api.bindevent($("form#set-form"));
            
            $('#c-level3').change(function() {
            	if($('#c-level3').val()!="") {
                	$("#c-div-percent3").show();
            	} else {
                	$("#c-div-percent3").hide();
            	}
            });
            
            $('#c-level4').change(function() {
            	if($('#c-level4').val()!="" && $('#c-level3').val()!="") {
                	$("#c-div-percent4").show();
            	} else {
                	$("#c-div-percent4").hide();
            	}
            });
        },
        setmaintain: function() {
            Controller.api.bindevent();
        },
        setmall: function() {
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