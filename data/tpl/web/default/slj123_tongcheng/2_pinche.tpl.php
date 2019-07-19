<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">拼车信息</h3>
	</div>
	<div class="panel-body table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th style="width: 10%; text-align: center;">类型</th>
					<th style="width: 15%; text-align: center;">用户名<br/>手机号</th>
					<th style="width: 20%; text-align: center;">出发地<br/>目的地</th>
					<th style="width: 10%; text-align: center;">出发时间</th>
					<th style="width: 10%; text-align: center;">空位<br/>人数<br/>重量</th>
					<th style="width: 20%; text-align: center;">备注</th>
					<th style="width: 5%; text-align: center;">操作</th>
				</tr>
			</thead>
			<tbody>
			<?php  if(is_array($list)) { foreach($list as $item) { ?>
				<tr>
					<td style="text-align: center;"><span><?php  if($item['type']==1) { ?>人找车<?php  } ?><?php  if($item['type']==2) { ?>车找人<?php  } ?><?php  if($item['type']==3) { ?>车找货<?php  } ?><?php  if($item['type']==4) { ?>货找车<?php  } ?></span></td>
					<td style="text-align: center;">
						<span class="name"><?php  echo $item['name'];?><br /><?php  echo $item['phone'];?></span>
					</td>
					<td style="text-align: center;"><?php  echo $item['from_place'];?><br /><?php  echo $item['to_place'];?></td>
					<td style="text-align: center;"><?php  echo $item['start_time'];?></td>
					<td style="text-align: center;"><?php  echo $item['user_count'];?></td>
					<td style="text-align: left;"><?php  echo $item['note'];?></td>
					<td>
						<a href="./index.php?c=site&a=entry&op=del&id=<?php  echo $item['id']?>&do=pinche&m=slj123_tongcheng"><div class="btn btn-danger">删除</div></a>
						&nbsp;&nbsp;
						<!--<a href="./index.php?c=site&a=entry&op=top&id=<?php  echo $item['id']?>&do=carpool&m=slj123_tongcheng"><div class="btn btn-info">置顶</div></a>-->
						
					</td>
				</tr>
			<?php  } } ?>
			</tbody>
		</table>
	</div>
	<?php  echo $pager;?>
</div>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>