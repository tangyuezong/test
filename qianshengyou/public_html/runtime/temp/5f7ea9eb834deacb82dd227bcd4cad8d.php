<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:93:"/www/web/qianshengyou/public_html/public/../application/admin/view/order/bedorder/rtnbed.html";i:1557390875;s:76:"/www/web/qianshengyou/public_html/application/admin/view/layout/default.html";i:1557390875;s:73:"/www/web/qianshengyou/public_html/application/admin/view/common/meta.html";i:1557390875;s:75:"/www/web/qianshengyou/public_html/application/admin/view/common/script.html";i:1557390875;}*/ ?>
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
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Order_no'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-order_no" data-rule="required" class="form-control form-control" name="order_no" type="text" value="<?php echo $row['order_no']; ?>" readonly="readonly">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Dev_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-dev_id" class="form-control form-control" name="row[dev_id]" type="text" value="<?php echo $row['dev_id']; ?>" readonly="readonly">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Entity_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-entity_id" data-source="device/entity/index" class="form-control selectpage form-control" name="row[entity_id]" type="text" value="<?php echo $row['entity_id']; ?>" disabled="disabled">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Netpoint_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-netpoint_id" data-source="device/netpoint/index" class="form-control selectpage form-control" name="row[netpoint_id]" type="text" value="<?php echo $row['netpoint_id']; ?>" disabled="disabled">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Canusestart'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-canusestart" data-date-format="YYYY-MM-DD HH:mm:ss" data-use-current="false" class="datetimepicker form-control" name="row[canusestart]" type="text" value="<?php echo datetime($row['canusestart']); ?>" readonly="readonly">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Canuseend'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-canuseend" data-date-format="YYYY-MM-DD HH:mm:ss" data-use-current="false" class="datetimepicker form-control" name="row[canuseend]" type="text" value="<?php echo datetime($row['canuseend']); ?>" readonly="readonly">
        </div>
    </div>
    <?php if(($row['pricetype']==1)): ?>
    	<div class="form-group">
	        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Pricetype'); ?>:</label>
	        <div class="col-xs-12 col-sm-8">
	            <input id="c-pricetype" class="form-control form-control" name="row[pricetype]" type="text" value="按小时计费" readonly="readonly">
	        </div>
	    </div>
	    <div class="form-group">
	        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Hourprice'); ?>:</label>
	        <div class="col-xs-12 col-sm-8">
	            <input id="c-hourprice" class="form-control form-control" name="row[hourprice]" type="number" value="<?php echo $row['hourprice']; ?>" readonly="readonly">
	        </div>
	    </div>
    <?php endif; if(($row['pricetype']==2)): ?>
    	<div class="form-group">
	        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Pricetype'); ?>:</label>
	        <div class="col-xs-12 col-sm-8">
	            <input id="c-pricetype" class="form-control form-control" name="row[pricetype]" type="text" value="按次计费" readonly="readonly">
	        </div>
	    </div>
    <?php endif; if(($row['pricetype']==3)): ?>
    	<div class="form-group">
	        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Pricetype'); ?>:</label>
	        <div class="col-xs-12 col-sm-8">
	            <input id="c-pricetype" class="form-control form-control" name="row[pricetype]" type="text" value="按天计费" readonly="readonly">
	        </div>
	    </div>
    <?php endif; ?>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Deposit'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-deposit" class="form-control form-control" name="row[deposit]" type="number" value="<?php echo $row['deposit']; ?>" readonly="readonly">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Total'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-total" class="form-control form-control" name="row[total]" type="number" value="<?php echo $row['total']; ?>" readonly="readonly">
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