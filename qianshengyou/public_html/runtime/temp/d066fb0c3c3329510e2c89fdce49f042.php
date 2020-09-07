<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:87:"/www/web/qianshengyou/public_html/public/../application/admin/view/users/user/edit.html";i:1557390875;s:76:"/www/web/qianshengyou/public_html/application/admin/view/layout/default.html";i:1557390875;s:73:"/www/web/qianshengyou/public_html/application/admin/view/common/meta.html";i:1557390875;s:75:"/www/web/qianshengyou/public_html/application/admin/view/common/script.html";i:1557390875;}*/ ?>
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
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Phone'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-phone" data-rule="required" class="form-control form-control" name="row[phone]" type="text" value="<?php echo $row['phone']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Openid'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-openid" data-rule="required" class="form-control form-control" name="row[openid]" type="text" value="<?php echo $row['openid']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Nickname'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-nickname" data-rule="required" class="form-control form-control" name="row[nickname]" type="text" value="<?php echo $row['nickname']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Head_image'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-head_image" data-rule="required" class="form-control form-control" size="50" name="row[head_image]" type="text" value="<?php echo $row['head_image']; ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-head_image" class="btn btn-danger plupload" data-input-id="c-head_image" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="false" data-preview-id="p-head_image"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-head_image" class="btn btn-primary fachoose" data-input-id="c-head_image" data-mimetype="image/*" data-multiple="false"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-head_image"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-head_image"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Wxgender'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-wxgender" data-rule="required" class="form-control form-control" name="row[wxgender]" type="number" value="<?php echo $row['wxgender']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Brand'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-brand" data-rule="required" class="form-control form-control" name="row[brand]" type="text" value="<?php echo $row['brand']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Model'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-model" data-rule="required" class="form-control form-control" name="row[model]" type="text" value="<?php echo $row['model']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('City'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class='control-relative'><input id="c-city" data-rule="required" class="form-control form-control" data-toggle="city-picker" name="row[city]" type="text" value="<?php echo $row['city']; ?>"></div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Province'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-province" data-rule="required" class="form-control form-control" name="row[province]" type="text" value="<?php echo $row['province']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Balance'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-balance" data-rule="required" class="form-control form-control" name="row[balance]" type="number" value="<?php echo $row['balance']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Score'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-score" data-rule="required" class="form-control form-control" name="row[score]" type="number" value="<?php echo $row['score']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Credit'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-credit" data-rule="required" class="form-control form-control" name="row[credit]" type="number" value="<?php echo $row['credit']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Deposit'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-deposit" data-rule="required" class="form-control form-control" name="row[deposit]" type="number" value="<?php echo $row['deposit']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Realname'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-realname" data-rule="required" class="form-control form-control" name="row[realname]" type="text" value="<?php echo $row['realname']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Id_type'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-id_type" data-rule="required" class="form-control form-control" name="row[id_type]" type="number" value="<?php echo $row['id_type']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Id_no'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-id_no" data-rule="required" class="form-control form-control" name="row[id_no]" type="text" value="<?php echo $row['id_no']; ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Id_image'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-id_image" data-rule="required" class="form-control form-control" size="50" name="row[id_image]" type="text" value="<?php echo $row['id_image']; ?>">
                <div class="input-group-addon no-border no-padding">
                    <span><button type="button" id="plupload-id_image" class="btn btn-danger plupload" data-input-id="c-id_image" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="false" data-preview-id="p-id_image"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                    <span><button type="button" id="fachoose-id_image" class="btn btn-primary fachoose" data-input-id="c-id_image" data-mimetype="image/*" data-multiple="false"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                </div>
                <span class="msg-box n-right" for="c-id_image"></span>
            </div>
            <ul class="row list-inline plupload-preview" id="p-id_image"></ul>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Realstat'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-realstat" data-rule="required" class="form-control form-control" name="row[realstat]" type="number" value="<?php echo $row['realstat']; ?>">
        </div>
    </div>
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