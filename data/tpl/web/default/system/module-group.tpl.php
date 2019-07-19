<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template("common/header", TEMPLATE_INCLUDEPATH)) : (include template("common/header", TEMPLATE_INCLUDEPATH));?>
<div class="steps">
	<div class="steps-item steps-status-wait">
		<div class="steps-line"><span class="steps-num">1</span></div>
		<div class="steps-state">安装应用</div>
	</div>
	<div class="steps-item steps-status-finish">
		<div class="steps-line"><span class="steps-num">2</span></div>
		<div class="steps-state">分配应用权限</div>
	</div>
	<div class="steps-item steps-status-wait">
		<div class="steps-line"><span class="steps-num">3</span></div>
		<div class="steps-state">安装成功</div>
	</div>
</div>
<div class="clearfix">
	<h5 class="page-header">安装 <?php  if($action == 'module') { ?><?php  echo $module['title'];?><?php  } else { ?>模板<?php  } ?></h5>
	<div class="alert alert-info">
		您正在安装 <?php  if($action == 'module') { ?><?php  echo $module['title'];?> 模块<?php  } else { ?>模板<?php  } ?>. 请选择哪些公众号服务套餐组可使用
		<?php  if($action == 'module') { ?><?php  echo $module['title'];?> 功能<?php  } else { ?>该模板<?php  } ?> .
	</div>
	<div class="alert alert-info">
		默认将<?php  if($action == 'module') { ?><?php  echo $module['title'];?> 模块<?php  } else { ?>模板<?php  } ?>加入<span class="label label-info">所有服务</span>套餐服务
	</div>
	<form class="form-horizontal form we7-form" action="" method="post" id="form1">
		<h5 class="page-header">可用的公众号服务套餐组 <small>这里来定义哪些公众号服务套餐组可使用 <?php  if($action == 'module') { ?><?php  echo $module['title'];?> 功能<?php  } else { ?>该模板<?php  } ?></small></h5>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">公众号服务套餐组</label>
			<div class="col-sm-10 col-xs-12">
				<?php  if(is_array($module_group)) { foreach($module_group as $group) { ?>
				<div class="checkbox">
					<input id="checkbox-<?php  echo $group['id'];?>" type="checkbox" name="group[]" value="<?php  echo $group['id'];?>">
					<label for="checkbox-<?php  echo $group['id'];?>" class="ng-binding"><?php  echo $group['name'];?></label>
				</div>
				<?php  } } ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label"></label>
			<div class="col-sm-10 col-xs-12">
				<input type="submit" class="btn btn-primary" name="submit" value="确定继续安装 <?php  echo $module['title'];?>">
			</div>
		</div>
		<input type="hidden" name="flag" value="1">
		<input type="hidden" name="tid" value="<?php  echo $tid;?>">
	</form>
	<div class="distribution-steps">
		<div class="we7-margin-bottom-sm font-lg">分配应用权限的流程说明</div>
		<div class="steps-container">
			<div>
				<div class="num">1</div>
				<div class="title">
					<span class="wi wi-appjurisdiction"></span>添加应用权限组
				</div>
				<div class="content">
					设置应用权限组名称，选择需要添加的公众号应用、小程序应用、微站模板，保存提交。
					<div><a href="<?php  echo url('module/group/post')?>" class="color-default">去添加应用组 ></a></div>
				</div>
			</div>
			<div>
				<div class="num">2</div>
				<div class="title">
					<span class="wi wi-distribution"></span>分配应用到应用权限组
				</div>
				<div class="content">
					选择上方相应的应用权限组，把应用添加到组中。或者到应用权限中编辑应用权限组。
					<div><a href="<?php  echo url('module/group')?>" class="color-default">选择/去编辑应用组 ></a></div>
				</div>
			</div>
		</div>
	</div>
	<script>
		$('#form1').submit(function(){
			var num = $("input[name='group[]']:checked").length;
			if(num == 0) {
				return confirm("您没有选择可使用该模块/模板的公众号服务套餐组,模块/模板安装成功后可在 公众号服务套餐 编辑");
			}
			return true;
		});
	</script>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
