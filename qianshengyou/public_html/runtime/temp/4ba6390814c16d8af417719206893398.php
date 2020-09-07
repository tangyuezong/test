<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:98:"/www/web/qianshengyou/public_html/public/../application/admin/view/device/netpoint/setpercent.html";i:1557390875;s:76:"/www/web/qianshengyou/public_html/application/admin/view/layout/default.html";i:1557390875;s:73:"/www/web/qianshengyou/public_html/application/admin/view/common/meta.html";i:1557390875;s:75:"/www/web/qianshengyou/public_html/application/admin/view/common/script.html";i:1557390875;}*/ ?>
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
                                <form id="set-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Groupserial'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
        	<div class="form-inline" data-toggle="cxselect" data-selects="level1,level2,level3,level4">
            	<!-- <select class="company form-control" name="row[cid]" data-url="ajax/compcartype" data-rule="required" ></select>
            	<select id="c-cartype" class="cartype form-control" name="row[ctid]" data-url="ajax/compcartype" data-rule="required" ></select>
            	 -->
            	<select class="level1 form-control" name="level1" data-url="ajax/authgroup" data-rule="required">
            		<option value="<?php echo $row['level1']; ?>" selected></option>
            	</select>
                <select id="c-level2" class="level2 form-control" name="level2" data-url="ajax/authgroup" data-rule="required">
                	<option value="<?php echo $row['level2']; ?>" selected></option>
                </select>
                <select id="c-level3" class="level3 form-control" name="level3" data-url="ajax/authgroup">
                	<option value="<?php echo $row['level3']; ?>" selected></option>
                </select>
                <select id="c-level4" class="level4 form-control" name="level4" data-url="ajax/authgroup">
                	<option value="<?php echo $row['level4']; ?>" selected></option>
                </select>
            	<span class="msg-box n-right" for="c-level2"></span>
        	</div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Percent1'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-percent1" data-rule="required" class="form-control" name="percent1" type="number" value="<?php echo $row['percent1']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Percent2'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-percent2" data-rule="required" class="form-control" name="percent2" type="number" value="<?php echo $row['percent2']; ?>">
        </div>
    </div>
    <div class="form-group" id="c-div-percent3" style="display:none;">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Percent3'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-percent3" class="form-control" name="percent3" type="number"  value="<?php echo $row['percent3']; ?>" placeholder="可选，若存在分润层级3，需设置">
        </div>
    </div>
    <div class="form-group" id="c-div-percent4" style="display:none;">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Percent4'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-percent4" class="form-control" name="percent4" type="number"  value="<?php echo $row['percent4']; ?>" placeholder="可选，若存在分润层级4，需设置">
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