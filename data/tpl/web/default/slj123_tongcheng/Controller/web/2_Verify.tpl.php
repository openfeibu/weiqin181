<?php defined('IN_IA') or exit('Access Denied');?><?PHP global $_W, $_GPC; ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<?php  if(is_array($GLOBALS['_W']['tabs'])) { foreach($GLOBALS['_W']['tabs'] as $key => $name) { ?>
		<li data-eval-1="<?php  if($GLOBALS['_GPC']['ac'] === $key) { ?>" class="active" data-eval-2="<?php  } ?>"><a href="<?php  echo $this->createWebUrl(basename(str_replace('\\', '/', get_class())), array('ac' => $key))?>"><?php  echo $name;?></a></li>
	<?php  } } ?>
</ul>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('Verify_' . $GLOBALS['_GPC']['ac'], TEMPLATE_INCLUDEPATH)) : (include template('Verify_' . $GLOBALS['_GPC']['ac'], TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>