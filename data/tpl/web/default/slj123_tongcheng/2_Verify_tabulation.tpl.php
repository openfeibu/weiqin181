<?php defined('IN_IA') or exit('Access Denied');?><div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">用户信息</h3>
	</div>
	<div class="panel-body table-responsive">
		<table class="table table-hover">
			<thead>
			<tr>
				<th>用户名</th>
				<th>手机号</th>
				<th>性别</th>
				<th>操作</th>
			</tr>
			</thead>
			<tbody>
			<?php  if(is_array($GLOBALS['PARAMS']['list'])) { foreach($GLOBALS['PARAMS']['list'] as $item) { ?>
			<tr>
				<td><?php  echo $item['realname'];?></td>
				<td><?php  echo $item['phone'];?></td>
				<td><?php  if($item['sex']==1) { ?>男<?php  } else { ?>女<?php  } ?></td>
				<td><a href="<?php  echo $this->createWebUrl('Verify', array('user_id' => $item['id'], 'ac' => 'edit'))?>" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="top" title="编辑"><i class="fa fa-pencil"></i></a>
				<a href="<?php  echo $this->createWebUrl('Verify', array('user_id' => $item['id'], 'ac' => 'del'))?>" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="top" title="删除"><i class="">X</i></a></td>
				
			</tr>
			<?php  } } ?>
			</tbody>
		</table>
	</div>
	<?php  echo $pager;?>
</div>