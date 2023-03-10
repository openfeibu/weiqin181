<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');


function reply_search($condition = '', $params = array(), $pindex = 0, $psize = 10, &$total = 0) {
	if (!empty($condition)) {
		$where = " WHERE {$condition}";
	}
	$sql = "SELECT * FROM " . tablename('rule') . $where . " ORDER BY status DESC, displayorder DESC, id DESC";
	if ($pindex > 0) {
				$start = ($pindex - 1) * $psize;
		$sql .= " LIMIT {$start},{$psize}";
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('rule') . $where, $params);
	}
	return pdo_fetchall($sql, $params);
}


function reply_single($id) {
	$id = intval($id);
	$result = table('rule')->getById($id);
	if (empty($result)) {
		return $result;
	}
	$result['keywords'] = table('rulekeyword')->whereRid($id)->getall();
	return $result;
}


function reply_keywords_search($condition = '', $params = array(), $pindex = 0, $psize = 10, &$total = 0) {
	if (!empty($condition)) {
		$where = " WHERE {$condition} ";
	}
	$sql = 'SELECT * FROM ' . tablename('rule_keyword') . $where . ' ORDER BY displayorder DESC, `type` ASC, id DESC';
	if ($pindex > 0) {
				$start = ($pindex - 1) * $psize;
		$sql .= " LIMIT {$start},{$psize}";
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('rule_keyword') . $where, $params);
	}
	$result = pdo_fetchall($sql, $params);
	if (!empty($result)) {
		foreach ($result as $key => $val) {
			$containtypes = pdo_get('rule', array('id' => $val['rid']), array('containtype'));
			if (!empty($containtypes)) {
				$containtype = explode(',', $containtypes['containtype']);
				$containtype = array_filter($containtype);
			} else {
				$containtype = array();
			}
			$result[$key]['reply_type'] = $containtype;
		}
	} else {
		$result = array();
	}
	return $result;
}


function reply_contnet_search($rid = 0) {
	$result = array();
	$rid = intval($rid);
	if (empty($rid)) {
		return $result;
	}

	$modules = array('basic', 'images', 'news', 'music', 'voice', 'video');
	$result = table('reply')->getModuleReplayCount($modules, $rid);
	return $result;
}


function reply_predefined_service() {
	$predefined_service = array(
		'weather.php' => array(
			'title' => '????????????',
			'description' => '"?????????+??????", ???: "????????????"',
			'keywords' => array(
					array('3', '^.+??????$')
			)
		),
		'baike.php' => array(
			'title' => '????????????',
			'description' => '"??????+????????????" ??? "??????+????????????", ???: "????????????", "???????????????"',
			'keywords' => array(
					array('3', '^??????.+$'),
					array('3', '^??????.+$'),
			)
		),
		'translate.php' => array(
			'title' => '????????????',
			'description' => '"@????????????(???????????????)"',
			'keywords' => array(
					array('3', '^@.+$'),
			)
		),
		'calendar.php' => array(
			'title' => '???????????????',
			'description' => '"??????", "?????????", "??????"???"??????"',
			'keywords' => array(
					array('1', '??????'),
					array('1', '?????????'),
					array('1', '??????'),
					array('1', '??????'),
			)
		),
		'news.php' => array(
			'title' => '?????????',
			'description' => '"??????"',
			'keywords' => array(
					array('1', '??????'),
			)
		),
		'express.php' => array(
			'title' => '????????????',
			'description' => '"??????+??????", ???: "??????1200041125"',
			'keywords' => array(
					array('3', '^(??????|??????|??????|??????|??????|??????|EMS) *[a-z0-9]{1,}$')
			)
		),
	);
	return $predefined_service;
}


function reply_getall_common_service() {
	global $_W;
	$rule_setting_select = table('uniaccountmodules')->whereUniacid($_W['uniacid'])->whereModule('userapi')->getcolumn('settings');
	$rule_setting_select = iunserializer($rule_setting_select);
	$exists_rule = table('rule')->where(array('uniacid' => 0, 'module' => 'userapi', 'status' => 1))->getall();
	$service_list = array();
	$rule_ids = array();
	$api_url = array();
	if (!empty($exists_rule)) {
		foreach ($exists_rule as $rule_detail) {
			$rule_ids[] = $rule_detail['id'];
			$service_list[$rule_detail['id']] = $rule_detail;
		}

		$all_description = table('userapireply')->whereRid($rule_ids)->getall();
		if (!empty($all_description)) {
			foreach ($all_description as $description) {
				$service_list[$description['rid']]['description'] = $description['description'];
				$service_list[$description['rid']]['switch'] = isset($rule_setting_select[$description['rid']]) && $rule_setting_select[$description['rid']] ? 'checked' : '';
				$api_url[] = $description['apiurl'];
			}
		}
	}

	$all_service = reply_predefined_service();
	$all_url = array_keys($all_service);
	$diff_url = array_diff($all_url, $api_url);
	if (!empty($diff_url)) {
		foreach ($diff_url as $url) {
			$service_list[$url]['id'] = $all_service[$url];
			$service_list[$url]['name'] = $all_service[$url]['title'];
			$service_list[$url]['description'] = $all_service[$url]['description'];
			$service_list[$url]['switch'] = '';
		}
	}
	return $service_list;
}


function reply_insert_without_service($file) {
	$rule_id = table('userapireply')->whereApiurl($file)->getcolumn('rid');
	if (!empty($rule_id)) {
		return $rule_id;
	}

	$all_service = reply_predefined_service();
	$all_url = array_keys($all_service);
	if (!in_array($file, $all_url)) {
		return false;
	}

	$rule_id = table('userapireply')->userapiSave($all_service, $file);
	return $rule_id;
}
