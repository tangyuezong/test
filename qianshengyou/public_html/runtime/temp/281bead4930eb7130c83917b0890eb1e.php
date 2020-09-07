<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:88:"/www/web/qianshengyou/public_html/public/../application/admin/view/device/price/add.html";i:1557390875;s:76:"/www/web/qianshengyou/public_html/application/admin/view/layout/default.html";i:1557390875;s:73:"/www/web/qianshengyou/public_html/application/admin/view/common/meta.html";i:1557390875;s:75:"/www/web/qianshengyou/public_html/application/admin/view/common/script.html";i:1557390875;}*/ ?>
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
                                <form id="add-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">

	<div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Admin_id'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-admin_id" data-rule="required" data-field="nickname" data-source="auth/admin/index" class="form-control selectpage form-control" name="row[admin_id]" type="text" value="">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Name'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-name" data-rule="required" class="form-control form-control" name="row[name]" type="text">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Lightstart'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo Form::timepicker('row[lightstart]', '', ['data-rule'=>'required','data-date-format'=>'HH:mm','data-use-current'=>'false']); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Lightend'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo Form::timepicker('row[lightend]', '', ['data-rule'=>'required','data-date-format'=>'HH:mm','data-use-current'=>'false']); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Nightstart'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo Form::timepicker('row[nightstart]', '', ['data-rule'=>'required','data-date-format'=>'HH:mm','data-use-current'=>'false']); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Nightend'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo Form::timepicker('row[nightend]', '', ['data-rule'=>'required','data-date-format'=>'HH:mm','data-use-current'=>'false']); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Hourdeposit'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-hourdeposit" data-rule="required" class="form-control form-control" name="row[hourdeposit]" type="number">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Hourprice'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-hourprice" data-rule="required" class="form-control form-control" name="row[hourprice]" type="number">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Timesdeposit'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-timesdeposit" data-rule="required" class="form-control form-control" name="row[timesdeposit]" type="number">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Lighttimesprice'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-lighttimesprice" data-rule="required" class="form-control form-control" name="row[lighttimesprice]" type="number">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Nighttimesprice'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-nighttimesprice" data-rule="required" class="form-control form-control" name="row[nighttimesprice]" type="number">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Daydeposit'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-daydeposit" data-rule="required" class="form-control form-control" name="row[daydeposit]" type="number">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Dayprice'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-dayprice" data-rule="required" class="form-control form-control" name="row[dayprice]" type="number">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Status'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="radio">
	            <?php if(is_array($statusList) || $statusList instanceof \think\Collection || $statusList instanceof \think\Paginator): if( count($statusList)==0 ) : echo "" ;else: foreach($statusList as $key=>$vo): ?>
	            <label for="row[status]-<?php echo $key; ?>"><input id="row[status]-<?php echo $key; ?>" name="row[status]" type="radio" value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',"1"))): ?>checked<?php endif; ?> /> <?php echo $vo; ?></label> 
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