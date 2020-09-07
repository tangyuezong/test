<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:90:"/www/web/qianshengyou/public_html/public/../application/admin/view/device/device/edit.html";i:1557390875;s:76:"/www/web/qianshengyou/public_html/application/admin/view/layout/default.html";i:1557390875;s:73:"/www/web/qianshengyou/public_html/application/admin/view/common/meta.html";i:1557390875;s:75:"/www/web/qianshengyou/public_html/application/admin/view/common/script.html";i:1557390875;}*/ ?>
<!DOCTYPE html>
<html lang="<?php echo $config['language']; ?>">
    <head>
        <meta charset="utf-8">
<title><?php echo (isset($title) && ($title !== '')?$title:''); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">

<link rel="shortcut icon" href="/assets/img/favicon.ico" />
<!-- Loading Bootstrap -->
<link href="/assets/css/backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
  <script src="/assets/js/html5shiv.js"></script>
  <script src="/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config:  <?php echo json_encode($config); ?>
    };
</script>
    </head>

    <body class="inside-header inside-aside <?php echo defined('IS_DIALOG') && IS_DIALOG ? 'is-dialog' : ''; ?>">
        <div id="main" role="main">
            <div class="tab-content tab-addtabs">
                <div id="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <section class="content-header hide">
                                <h1>
                                    <?php echo __('Dashboard'); ?>
                                    <small><?php echo __('Control panel'); ?></small>
                                </h1>
                            </section>
                            <?php if(!IS_DIALOG && !$config['fastadmin']['multiplenav']): ?>
                            <!-- RIBBON -->
                            <div id="ribbon">
                                <ol class="breadcrumb pull-left">
                                    <li><a href="dashboard" class="addtabsit"><i class="fa fa-dashboard"></i> <?php echo __('Dashboard'); ?></a></li>
                                </ol>
                                <ol class="breadcrumb pull-right">
                                    <?php foreach($breadcrumb as $vo): ?>
                                    <li><a href="javascript:;" data-url="<?php echo $vo['url']; ?>"><?php echo $vo['title']; ?></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                            <!-- END RIBBON -->
                            <?php endif; ?>
                            <div class="content">
                                <form id="edit-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Dev_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-dev_id" data-rule="required" class="form-control form-control" name="row[dev_id]" type="text" value="<?php echo $row['dev_id']; ?>">
        </div>
    </div>
    <div class="form-group">
    	<label class="control-label col-xs-12 col-sm-2"><?php echo __('Iscabinet'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
        	<select id="c-iscabinet" data-rule="required" class="form-control selectpicker" name="row[iscabinet]">
                <?php if(is_array($iscabinetList) || $iscabinetList instanceof \think\Collection || $iscabinetList instanceof \think\Paginator): if( count($iscabinetList)==0 ) : echo "" ;else: foreach($iscabinetList as $key=>$vo): ?>
                <option value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['iscabinet'])?$row['iscabinet']:explode(',',$row['iscabinet']))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Admin_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-admin_id" data-rule="required" data-field="nickname" data-source="auth/admin/index" class="form-control selectpage form-control" name="row[admin_id]" type="text" value="<?php echo $row['admin_id']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Price_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-price_id" data-rule="required" data-source="device/price/index" class="form-control selectpage form-control" name="row[price_id]" type="text" value="<?php echo $row['price_id']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Netpoint_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-netpoint_id" data-rule="required" data-source="device/netpoint/index" class="form-control selectpage form-control" name="row[netpoint_id]" type="text" value="<?php echo $row['netpoint_id']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Entity_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-entity_id" data-rule="required" data-source="device/entity/index" class="form-control selectpage form-control" name="row[entity_id]" type="text" value="<?php echo $row['entity_id']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Department'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-department" class="form-control form-control" name="row[department]" type="text" value="<?php echo $row['department']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Room'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-room" class="form-control form-control" name="row[room]" type="text" value="<?php echo $row['room']; ?>">
        </div>
    </div><div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Bed'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-bed" class="form-control form-control" name="row[bed]" type="text" value="<?php echo $row['bed']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Mac'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-mac" data-rule="required" class="form-control form-control" name="row[mac]" type="text" value="<?php echo $row['mac']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Blekey'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-blekey" data-rule="required" class="form-control form-control" name="row[blekey]" type="text" value="<?php echo $row['blekey']; ?>" readonly="readonly">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Blepwd'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-blepwd" data-rule="required" class="form-control form-control" name="row[blepwd]" type="text" value="<?php echo $row['blepwd']; ?>" readonly="readonly">
        </div>
    </div>
    <!-- <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Mac2'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-mac2" class="form-control form-control" name="row[mac2]" type="text" value="<?php echo $row['mac2']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Blekey2'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-blekey2" class="form-control form-control" name="row[blekey2]" type="text" value="<?php echo $row['blekey2']; ?>" readonly="readonly">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Blepwd2'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-blepwd2" class="form-control form-control" name="row[blepwd2]" type="text" value="<?php echo $row['blepwd2']; ?>" readonly="readonly">
        </div>
    </div> -->
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Status'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="radio">
	            <?php if(is_array($statusList) || $statusList instanceof \think\Collection || $statusList instanceof \think\Paginator): if( count($statusList)==0 ) : echo "" ;else: foreach($statusList as $key=>$vo): ?>
	            <label for="row[status]-<?php echo $key; ?>"><input id="row[status]-<?php echo $key; ?>" name="row[status]" type="radio" value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['status'])?$row['status']:explode(',',$row['status']))): ?>checked<?php endif; ?> /> <?php echo $vo; ?></label> 
	            <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>
    </div>
    <div class="form-group layer-footer">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <button type="submit" class="btn btn-success btn-embossed disabled"><?php echo __('OK'); ?></button>
            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
        </div>
    </div>
</form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo $site['version']; ?>"></script>
    </body>
</html>