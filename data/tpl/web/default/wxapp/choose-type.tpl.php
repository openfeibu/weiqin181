<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div id="js-wxapp-create" class="account-list-add">
	<ol class="breadcrumb we7-breadcrumb">
		<a href="<?php  echo url('wxapp/manage');?>"><i class="wi wi-back-circle"></i></a>
		<li><a href="<?php  echo url('wxapp/display');?>">小程序列表</a></li>
		<li>新建小程序</li>
	</ol>
	<div class="panel we7-panel">
		<?php  if(!$_W['isfounder'] && !empty($account_info['wxapp_limit'])) { ?>
		<div class="alert alert-warning hidden">
			温馨提示：
			<i class="fa fa-info-circle"></i>
			Hi，<span class="text-strong"><?php  echo $_W['username'];?></span>，您所在的会员组： <span class="text-strong"><?php  echo $account_info['group_name'];?></span>，
			账号有效期限：<span class="text-strong"><?php  echo date('Y-m-d', $_W['user']['starttime'])?> ~~ <?php  if(empty($_W['user']['endtime'])) { ?>无限制<?php  } else { ?><?php  echo date('Y-m-d', $_W['user']['endtime'])?><?php  } ?></span>，
			可创建 <span class="text-strong"><?php  echo $account_info['maxwxapp'];?> </span>个小程序，已创建<span class="text-strong"> <?php  echo $account_info['wxapp_num'];?> </span>个，还可创建 <span class="text-strong"><?php  echo $account_info['wxapp_limit'];?> </span>个小程序。
		</div>
		<?php  } ?>
		<div class="panel-body we7-padding">
			<div class="col-lg-6">
				<div class="title">
					<span class="img img-pen"></span>
					<a href="javascript:;">手动添加小程序</a>
				</div>
				<div class="con">
					手动绑定需同步微信接口，在微信后台，基本设置可以获取appid和appsecret，绑定成功后，将获取的服务器配置接口绑定到微信后台（切记：绑定过程中，一定要注意保持接口参数一致）
				</div>
				<div>
					<a href="<?php  echo url('wxapp/post/design_method', array('design_method' => WXAPP_MODULE, 'uniacid' => $uniacid, 'choose_type'=>1))?>" class="btn btn-primary">手动添加小程序</a>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="title">
					<span class="img img-tel"></span>
					授权添加小程序
				</div>
				<div class="con">
					使用授权登录需认证微信开放平台和全网发布，认证微信开放平台和全网发布教程 <a href="<?php  echo url('system/platform')?>" class="color-default">微信开放平台设置</a>
				</div>
				<div>
					<a href="<?php  echo $authurl;?>" class="btn btn-primary">授权添加小程序</a>
				</div>
			</div>
		</div>
	</div>
</div>

<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>