<?php
	//这个操作被定义用来呈现 管理中心导航菜单
		global $_W,$_GPC;
        load()->func('tpl');
        $op=!empty($_GPC['op'])?$_GPC['op']:'list';
        //$op =$_GPC['op'];
        //页面显示条数
        $pagesize=10;
        $page = max(1, $_GPC['page']);
        
        //表单提交
if(checksubmit()){
//******需要修改的地方******
$data=array(
	'uniacid'=>$_W['uniacid'],
	'img'=>$_GPC['img'],
	'pxh'=>trim($_GPC['pxh']),

	'enable'=>trim($_GPC['enable']),
	'crtime'=>time()
	
	);


if($op=='add'){

	$res=pdo_insert('hdp', $data);
//echo var_dump($data);exit;
	if($res){message('新增成功', $this->createWebUrl('banner_action'), 'success');
}else{
	essage('新增失败', $this->createWebUrl('banner_action'), 'error');
}
return;
}else if($op=='edit'){
	$id=$_GPC['id'];
	$res=pdo_update('hdp', $data,array('img' => $_GPC['img'],'pxh'=>trim($_GPC['pxh']),'enable'=>trim($_GPC['enable'])) );

	if($res){message('修改成功', $this->createWebUrl('banner_action'), 'success');
}else{
	message('修改失败', $this->createWebUrl('banner_action'), 'error');
}
return;
}else if($op=='list'){
 
}

}

//列表批量删除
if(checksubmit('delete_selectd')){
$res = pdo_delete('hdp',array('id'=>$_GPC['ids']));
if($res){message('删除'.count($_GPC['ids']).'条成功!',referer(), 'success');
}else{message('删除失败!',referer(), 'error');}
return;	
}
        
        if($op=='del'){
            $id=$_GPC['id'];	
            $res = pdo_delete('hdp',array('id'=>$id));
            if($res){
            	message('删除成功!',referer(), 'success');
            }else{
            	message('删除失败!',referer(), 'error');
            }
        }else if($op=='add'){
              /*增加表单*/
        //表单初始化开始
        //message('新增成功', $op, 'success');
        $det['pxh']=0;//排序
     
        //表单初始化结束
        $pagediv='form';
        //所有消息分类
        //$mtypelist=$DBUtil->getMany($DBUtil::wys_tongcheng_mtype,' uniacid=:uniacid and enable=:enable',array(':uniacid'=>$_W['uniacid'],':enable'=>1));
        include $this->template('web/banner_page');
       
        return;	
       
       }else if($op=='list'||$op==''){
         //message('新增成功', $op, 'success');
        //var_dump($op.'1');exit;
       $pagediv='list';
       // $where='uniacid=:uniacid';
        //$param=array(':uniacid'=>$_W['uniacid']);
        $total=pdo_fetchcolumn('SELECT COUNT(*) AS `number` FROM ' . tablename('hdp') . ' WHERE uniacid=:uniacid' , array(':uniacid'=>$_W['uniacid']));

        //生成分页HTML
        $result['pager']=pagination($total, $page, $pagesize);
        $list = pdo_fetchall('SELECT * FROM ' . tablename('hdp') . ' WHERE uniacid=:uniacid ORDER BY pxh asc', array(':uniacid'=>$_W['uniacid']));
       //var_dump($list);exit;
       include $this->template('web/banner_page');

       return;	
       }else if($op=='edit'){
        //$id=$_GPC['id'];
        //$where='uniacid=:uniacid and id=:id';
        //$param=array(':uniacid'=>$_W['uniacid'],':id'=>$_GPC['id']);
        $det=pdo_fetch('SELECT * FROM ' . tablename('hdp') . ' WHERE id=:id', array(':id'=>$_GPC['id'],':uniacid'=>$_W['uniacid']));
        //$start_diqu = $DBUtil->getDiqus('uniacid=:uniacid AND attr=:attr', array(':uniacid'=>$_W['uniacid'], ':attr'=>0));
        //所有消息分类
        //$mtypelist=$DBUtil->getMany($DBUtil::wys_tongcheng_mtype,' uniacid=:uniacid and enable=:enable',array(':uniacid'=>$_W['uniacid'],':enable'=>1));
        
        //var_dump($id);exit;
        include $this->template('web/banner_page');
        return;
        }
       
       //var_dump($op);exit;
