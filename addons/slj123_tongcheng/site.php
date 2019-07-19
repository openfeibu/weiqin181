<?php
defined('IN_IA') or exit('Access Denied');
include_once dirname(__FILE__) . '/autoload.inc.php';

class Slj123_tongchengModuleSite extends WeModuleSite
{
    public function __construct() { }

    public function __call($name, $arguments)
    {
        $dir = IA_ROOT . '/addons/' . $this->modulename . '/inc/'; $sub = ''; $fun = '';

        $class = ModuleNamePascal() . '\controller\\';
        $name = str_replace('-', '\\', $name);
        stripos($name, 'doWeb') === 0 && ($sub = 'web\\') && ($fun = substr($name, 5));
        stripos($name, 'doMobile') === 0 && ($sub = 'mobile\\') && ($fun = $GLOBALS['_GPC']['do']/*substr($name, 8)*/);
        class_exists($class .= $sub . $fun) && (new $class($this, $arguments) && die || die);

        (0 === stripos($name, 'doWeb')) && ($sub = 'web/') && ($fun = /*strtolower*/(substr($name, 5)));
        (0 === stripos($name, 'doMobile')) && ($sub = 'mobile/') && ($fun = /*strtolower*/(substr($name, 8)));
        is_file($file = $dir . $sub . $fun . '.inc.php') && ((require $file) && die || die);

        (0 === stripos($name, 'doWeb')) && ($sub = 'web/') && ($fun = strtolower(substr($name, 5)));
        (0 === stripos($name, 'doMobile')) && ($sub = 'mobile/') && ($fun = strtolower(substr($name, 8)));
        is_file($file = $dir . $sub . $fun . '.inc.php') && ((require $file) && die || die);

        trigger_error("访问的方法：<br />\n \$file:{$file}<br />\n \$name:{$name}<br />\n \$class:{$class}<br />\n 不存在.", E_USER_WARNING);
        return null;
    }

    public function doWebBasic()
    {
        global $_W, $_GPC;
        $weid = $_W['uniacid'];

        $sql = 'SELECT * FROM ' . tablename('slj123_pc_setting') . ' WHERE `weid` = :weid';
        $config = pdo_fetch($sql, array(':weid' => $weid));
        $config['banner'] = explode(',', $config['banner']);

        if (checksubmit()) {

            if (empty($_GPC['people_post_cost'])) {
                message('请输入车找人发布金额', '', error);
            }
            if (empty($_GPC['people_post_cost'])) {
                message('请输入人找车发布金额', '', error);
            }
            if (empty($_GPC['people_post_cost'])) {
                message('请输入车找货发布金额', '', error);
            }
            if (empty($_GPC['people_post_cost'])) {
                message('请输入货找车发布金额', '', error);
            }

            $input = array_elements(array(
                'car_post_cost', 'car_post_cost1', 'car_post_cost2', 'people_post_cost', 'top_time_cost', 'banner','notice_title', 
                'notice_content', 'share_title', 'share_pic', 'share_des', 'banner_link', 'notice','isopen_renzheng','unitid'
            ), $_GPC);

            $input['car_post_cost'] = trim($input['car_post_cost']);
            $input['car_post_cost1'] = trim($input['car_post_cost1']);
            $input['car_post_cost2'] = trim($input['car_post_cost2']);
            $input['people_post_cost'] = trim($input['people_post_cost']);
            $input['top_time_cost'] = trim($input['top_time_cost']);
            $input['banner'] = trim(implode(',', array_filter($input['banner'])));
            $input['banner_link'] = trim($input['banner_link']);
            $input['notice_title'] = trim($input['notice_title']);
            $input['notice_content'] = trim($input['notice_content']);
            $input['notice'] = trim($input['notice']);
            $input['share_title'] = trim($input['share_title']);
            $input['share_pic'] = trim($input['share_pic']);
            $input['share_des'] = trim($input['share_des']);
			$input['isopen_renzheng'] = trim($input['isopen_renzheng']);
			$input['unitid']=trim($input['unitid']);
            if (empty($config['id'])) {
                $input['weid'] = $weid;
                pdo_insert('slj123_pc_setting', $input);
            } else {
                pdo_update('slj123_pc_setting', $input, array('weid' => $weid));
            }

            message('设置成功', 'refresh', 'success');
        }
        include $this->template('basic');
    }

    public function doWebPinche()
    {
        global $_W, $_GPC;
        $weid = $_W['uniacid'];

        if ($_GPC['op'] == 'del') {
            $result = pdo_delete('slj123_pc_index', array('weid' => $weid, 'id' => $_GPC['id']));
            if (!empty($result)) {
                message('删除成功', 'refresh');
            }
        }

        $pindex = $_GPC['page'];
        if (empty($pindex) || $pindex < 0) {
            $pindex = 1;
        }
        $psize = 10;
        $sql = 'SELECT count(*) FROM ' . tablename('slj123_pc_index') . ' WHERE `weid` = :weid';
        $total = pdo_fetchcolumn($sql, array(':weid' => $weid));

        $pager = pagination($total, $pindex, $psize);

        $sql = 'SELECT * FROM ' . tablename('slj123_pc_index') . ' WHERE `weid` = :weid LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
        $list = pdo_fetchall($sql, array(':weid' => $weid));

        include $this->template('pinche');
    }

    public function doWebMember()
    {

        global $_W, $_GPC;
        $weid = $_W['uniacid'];

        $pindex = $_GPC['page'];
        if (empty($pindex) || $pindex < 0) {
            $pindex = 1;
        }
        $psize = 10;
        $sql = "SELECT count(*) FROM " . tablename('slj123_pc_user') . " WHERE `weid` = :weid LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
        $total = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('slj123_pc_user') . " WHERE `weid` = :weid", array(':weid' => $weid));

        $pager = pagination($total, $pindex, $psize);

        $member = pdo_fetchall("SELECT * FROM " . tablename('slj123_pc_user') . " WHERE `weid` = :weid LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $weid));

        include $this->template('member');
    }

}