<?php
	//这个操作被定义用来呈现 管理中心导航菜单
		global $_W,$_GPC;
        load()->func('tpl');
        $op=!empty($_GPC['op'])?$_GPC['op']:'list';
        //$op =$_GPC['op'];
        //页面显示条数
        $pagesize=10;
        $page = max(1, $_GPC['page']);
        
       

        //列表批量删除
        if(checksubmit('delete_selectd')){
        $res = pdo_delete('pingche_msg',array('id'=>$_GPC['ids']));
        if($res){message('删除'.count($_GPC['ids']).'条成功!',referer(), 'success');
        }else{message('删除失败!',referer(), 'error');}
        return;	
        }
        
        if($op=='del'){
            $id=$_GPC['id'];	
            $res = pdo_delete('pingche_msg',array('id'=>$id));
            if($res){
            	message('删除成功!',referer(), 'success');
            }else{
            	message('删除失败!',referer(), 'error');
            }
        }else if($op=='list'||$op==''){
         //message('新增成功', $op, 'success');
        //var_dump($op.'1');exit;
       $pagediv='list';
       // $where='uniacid=:uniacid';
        //$param=array(':uniacid'=>$_W['uniacid']);
        $total=pdo_fetchcolumn('SELECT COUNT(*) AS `number` FROM ' . tablename('pingche_msg') . ' WHERE uniacid=:uniacid' , array(':uniacid'=>$_W['uniacid']));

        //生成分页HTML
        $result['pager']=pagination($total, $page, $pagesize);
        $list = pdo_fetchall('SELECT * FROM ' . tablename('pingche_msg') . ' WHERE uniacid=:uniacid ', array(':uniacid'=>$_W['uniacid']));
      // var_dump($list);exit;
       include $this->template('web/pinche');

       return;	
       }
       
       //var_dump($op);exit;
