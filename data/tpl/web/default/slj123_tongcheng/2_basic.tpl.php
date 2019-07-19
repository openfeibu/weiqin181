<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<form class="form-horizontal" action="" method="post">

	<div class="panel panel-default">

		<div class="panel-heading">
			<h3 class="panel-title">基础设置</h3>
		</div>

		<div class="panel-body">
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">幻灯片</label>
				<div class="col-xs-12 col-sm-8">
					<?php  echo tpl_form_field_multi_image('banner',$config['banner']);?>
					<span class="help-block">(建议尺寸：640*160)<span style=" color: #f00; ">可以多张</span></span>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">幻灯片链接</label>
				<div class="col-xs-12 col-sm-8">
					<input type="text" class="form-control" name="banner_link" value="<?php  echo $config['banner_link'];?>" />
					<span class="help-block">配置幻灯片链接，请跟幻灯片数量一致，用<span style=" color: #f00; ">半角逗号</span>隔开（<span style=" color: #f00; ">,</span>）</span>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">人找车发布金额</label>
				<div class="col-xs-12 col-sm-8">
					<input type="text" class="form-control" name="car_post_cost" value="<?php  echo $config['car_post_cost'];?>" />
					<span class="help-block">(发布时需要支付的金额,不配置支付责免费发布!)</span>
				</div>	
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">车找人发布金额</label>
				<div class="col-xs-12 col-sm-8">
					<input type="text" class="form-control" name="people_post_cost" value="<?php  echo $config['people_post_cost'];?>" />
					<span class="help-block">(发布时需要支付的金额，不配置支付责免费发布!)</span>
				</div>	
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">车找货发布金额</label>
				<div class="col-xs-12 col-sm-8">
					<input type="text" class="form-control" name="car_post_cost1" value="<?php  echo $config['car_post_cost1'];?>" />
					<span class="help-block">(发布时需要支付的金额,不配置支付责免费发布!)</span>
				</div>	
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">货找车发布金额</label>
				<div class="col-xs-12 col-sm-8">
					<input type="text" class="form-control" name="car_post_cost2" value="<?php  echo $config['car_post_cost2'];?>" />
					<span class="help-block">(发布时需要支付的金额,不配置支付责免费发布!)</span>
				</div>	
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">付费置顶金额</label>
				<div class="col-xs-12 col-sm-8">
					<input type="text" class="form-control" name="top_time_cost" value="<?php  echo $config['top_time_cost'];?>" />
					<span class="help-block">(置顶<span style=" color: #f00; ">1天</span>需要支付的金额,不配置支付责免费发布!)</span>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">分享标题</label>
				<div class="col-xs-12 col-sm-8">
					<input type="text" class="form-control" name="share_title" value="<?php  echo $config['share_title'];?>" />
					<span class="help-block">(第三方分享)</span>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">分享描述</label>
				<div class="col-xs-12 col-sm-8">
					<input type="text" class="form-control" name="share_des" value="<?php  echo $config['share_des'];?>" />
					<span class="help-block"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">提示标题</label>
				<div class="col-xs-12 col-sm-8">
					<input type="text" class="form-control" name="notice_title" value="<?php  echo $config['notice_title'];?>" />
					<span class="help-block"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">车主是否必须认证</label>
				<div class="col-xs-12 col-sm-8">
					<input type="checkbox" lay-skin="switch"  name="isopen_renzheng" <?php  if($config['isopen_renzheng']==1) { ?>checked="true"<?php  } ?> lay-text="是|否" value="1"/>
				</div>
			</div>
			<!--<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">是否关闭短信认证</label>
				<div class="col-xs-12 col-sm-8">
					<input type="checkbox" lay-skin="switch"  name="isopen_dxrz" <?php  if($config['isopen_dxrz']==1) { ?>checked="true"<?php  } ?> lay-text="是|否" value="1"/>
				</div>
			</div>-->
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">免责声明</label>
				<div class="col-xs-12 col-sm-8">
					<input  type="text" class="form-control" name="notice_content" value="<?php  echo $config['notice_content'];?>" />
					<span class="help-block"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">公告</label>
				<div class="col-xs-12 col-sm-8">
					<input type="text" class="form-control" name="notice" value="<?php  echo $config['notice'];?>" />
					<span class="help-block"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">分享图片</label>
				<div class="col-xs-12 col-sm-8">
					<?php  echo tpl_form_field_image('share_pic',$config['share_pic']);?>
					<span class="help-block">(支持PNG及JPG，不设置则使用默认截图。)</span>
				</div>
			</div>
          <div class="form-group">
				<label class="control-label col-xs-12 col-sm-2">流量主配置unit-id</label>
				<div class="col-xs-12 col-sm-8">
					<input type="text" class="form-control" name="unitid" value="<?php  echo $config['unitid'];?>" />
					<span class="help-block">请填写adunit-xxxxxxxxx格式</span>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-xs-12 col-sm-2"></label>
				<div class="col-xs-12 col-sm-8">
					<input type="submit" name="submit" class="btn btn-primary" value="提交" />
					<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
				</div>
			</div>

		</div>

	</div>

</form>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>