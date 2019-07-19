<?php defined('IN_IA') or exit('Access Denied');?><?PHP global $_W, $_GPC; ?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="form1">
		<div class="panel panel-default">
			<div class="panel-heading">随机短信验证码设置</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">阿里云短信$accessKeyId</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="settings[phone_random_code][$accessKeyId]" class="form-control" value="<?php  echo $settings['phone_random_code']['$accessKeyId'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">阿里云短信$accessKeySecret</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="settings[phone_random_code][$accessKeySecret]" class="form-control" value="<?php  echo $settings['phone_random_code']['$accessKeySecret'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">模板ID</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="settings[phone_random_code][$setTemplateCode]" class="form-control" value="<?php  echo $settings['phone_random_code']['$setTemplateCode'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">签名</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="settings[phone_random_code][$setSignName]" class="form-control" value="<?php  echo $settings['phone_random_code']['$setSignName'];?>" />
					</div>
				</div>
			</div>
		</div>
		
		<div class="form-group col-sm-12">
			<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
			<input type="hidden" name="member[uniacid]" value="<?php  echo $GLOBALS['_W']['uniacid'];?>" />
			<input type="hidden" name="member[id]" value="<?php  echo $member['id'];?>" />
			<input type="hidden" name="token" value="<?php  echo $GLOBALS['_W']['token'];?>" />
		</div>
	</form>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
