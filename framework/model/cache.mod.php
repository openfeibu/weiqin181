<?php

/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
function cache_build_template() {
	load()->func('file');
	rmdirs(IA_ROOT . '/data/tpl', true);
}


function cache_build_setting() {
	$sql = "SELECT * FROM " . tablename('core_settings');
	$setting = pdo_fetchall($sql, array(), 'key');
	if (is_array($setting)) {
		foreach ($setting as $k => $v) {
			$setting[$v['key']] = iunserializer($v['value']);
		}
		cache_write("setting", $setting);
	}
}


function cache_build_module_status() {
	load()->model('cloud');
	$cloud_modules = cloud_m_query();
	$module_ban = is_array($cloud_modules['pirate_apps']) ? $cloud_modules['pirate_apps'] : array();
	$local_module = setting_load('module_ban');
	$update_modules = array_merge(array_diff($local_module, $module_ban), array_diff($module_ban, $local_module));
	if (!empty($update_modules)) {
		foreach ($update_modules as $module) {
			cache_build_module_info($module);
		}
	}
	setting_save($module_ban, 'module_ban');
	setting_save(array(), 'module_upgrade');
}


function cache_build_account_modules($uniacid = 0) {
	$uniacid = intval($uniacid);
	if (empty($uniacid)) {
				cache_clean(cache_system_key('unimodules'));
		cache_clean(cache_system_key('user_modules'));
	} else {
		cache_delete(cache_system_key("unimodules:{$uniacid}:1"));
		cache_delete(cache_system_key("unimodules:{$uniacid}:"));
		$owner_uid = pdo_getcolumn('uni_account_users', array('role' => 'owner'), 'uid');
		cache_delete(cache_system_key("user_modules:{$owner_uid}:"));
	}
}


function cache_build_account($uniacid = 0) {
	global $_W;
	$uniacid = intval($uniacid);
	if (empty($uniacid)) {
		$uniacid_arr = pdo_fetchall("SELECT uniacid FROM " . tablename('uni_account'));
		foreach($uniacid_arr as $account){
			cache_delete("uniaccount:{$account['uniacid']}");
			cache_delete("unisetting:{$account['uniacid']}");
			cache_delete("defaultgroupid:{$account['uniacid']}");
		}
	} else {
		cache_delete("uniaccount:{$uniacid}");
		cache_delete("unisetting:{$uniacid}");
		cache_delete("defaultgroupid:{$uniacid}");
	}

}


function cache_build_memberinfo($uid) {
	$uid = intval($uid);
	$cachekey = cache_system_key(CACHE_KEY_MEMBER_INFO, $uid);
	cache_delete($cachekey);
	return true;
}


function cache_build_users_struct() {
	$base_fields = array(
		'uniacid' => '???????????????id',
		'groupid' => '??????id',
		'credit1' => '??????',
		'credit2' => '??????',
		'credit3' => '??????????????????3',
		'credit4' => '??????????????????4',
		'credit5' => '??????????????????5',
		'credit6' => '??????????????????6',
		'createtime' => '????????????',
		'mobile' => '????????????',
		'email' => '????????????',
		'realname' => '????????????',
		'nickname' => '??????',
		'avatar' => '??????',
		'qq' => 'QQ???',
		'gender' => '??????',
		'birth' => '??????',
		'constellation' => '??????',
		'zodiac' => '??????',
		'telephone' => '????????????',
		'idcard' => '????????????',
		'studentid' => '??????',
		'grade' => '??????',
		'address' => '??????',
		'zipcode' => '??????',
		'nationality' => '??????',
		'reside' => '?????????',
		'graduateschool' => '????????????',
		'company' => '??????',
		'education' => '??????',
		'occupation' => '??????',
		'position' => '??????',
		'revenue' => '?????????',
		'affectivestatus' => '????????????',
		'lookingfor' => ' ????????????',
		'bloodtype' => '??????',
		'height' => '??????',
		'weight' => '??????',
		'alipay' => '???????????????',
		'msn' => 'MSN',
		'taobao' => '????????????',
		'site' => '??????',
		'bio' => '????????????',
		'interest' => '????????????',
		'password' => '??????',
		'pay_password' => '????????????',
	);
	cache_write('userbasefields', $base_fields);
	$fields = pdo_getall('profile_fields', array(), array(), 'field');
	if (!empty($fields)) {
		foreach ($fields as &$field) {
			$field = $field['title'];
		}
		$fields['uniacid'] = '???????????????id';
		$fields['groupid'] = '??????id';
		$fields['credit1'] ='??????';
		$fields['credit2'] = '??????';
		$fields['credit3'] = '??????????????????3';
		$fields['credit4'] = '??????????????????4';
		$fields['credit5'] = '??????????????????5';
		$fields['credit6'] = '??????????????????6';
		$fields['createtime'] = '????????????';
		$fields['password'] = '????????????';
		$fields['pay_password'] = '????????????';
		cache_write('usersfields', $fields);
	} else {
		cache_write('usersfields', $base_fields);
	}
}

function cache_build_frame_menu() {
	global $_W;
	$system_menu_db = pdo_getall('core_menu', array('permission_name !=' => ''), array(), 'permission_name');
	$system_menu = require IA_ROOT . '/web/common/frames.inc.php';
	if (!empty($system_menu) && is_array($system_menu)) {
		$system_displayoder = 1;
		foreach ($system_menu as $menu_name => $menu) {
			$system_menu[$menu_name]['is_system'] = true;
			$system_menu[$menu_name]['is_display'] = !empty($system_menu_db[$menu_name]['is_display']) ? true : ((isset($system_menu[$menu_name]['is_display']) && empty($system_menu[$menu_name]['is_display']) || !empty($system_menu_db[$menu_name])) ? false : true);
			$system_menu[$menu_name]['displayorder'] = !empty($system_menu_db[$menu_name]) ? intval($system_menu_db[$menu_name]['displayorder']) : ++$system_displayoder;
			foreach ($menu['section'] as $section_name => $section) {
				$displayorder = max(count($section['menu']), 1);

								if (empty($section['menu'])) {
					$section['menu'] = array();
				}
				$add_menu = pdo_getall('core_menu', array('group_name' => $section_name), array(
					'id', 'title', 'url', 'is_display', 'is_system', 'permission_name', 'displayorder', 'icon',
				), 'permission_name', 'displayorder DESC');
				if (!empty($add_menu)) {
					foreach ($add_menu as $permission_name => $menu) {
						$menu['icon'] = !empty($menu['icon']) ? $menu['icon'] : 'wi wi-appsetting';
						$section['menu'][$permission_name] = $menu;
					}
				}
				$section_hidden_menu_count = 0;
				foreach ($section['menu']  as $permission_name => $sub_menu) {
					$sub_menu_db = $system_menu_db[$sub_menu['permission_name']];
					$system_menu[$menu_name]['section'][$section_name]['menu'][$permission_name] = array(
						'is_system' => isset($sub_menu['is_system']) ? $sub_menu['is_system'] : 1,
						'is_display' => isset($sub_menu['is_display']) && empty($sub_menu['is_display']) ? 0 : (isset($sub_menu_db['is_display']) ? $sub_menu_db['is_display'] : 1),
						'title' => !empty($sub_menu_db['title']) ? $sub_menu_db['title'] : $sub_menu['title'],
						'url' => $sub_menu['url'],
						'permission_name' => $sub_menu['permission_name'],
						'icon' => $sub_menu['icon'],
						'displayorder' => !empty($sub_menu_db['displayorder']) ? $sub_menu_db['displayorder'] : $displayorder,
						'id' => $sub_menu['id'],
						'sub_permission' => $sub_menu['sub_permission'],
					);
					$displayorder--;
					$displayorder = max($displayorder, 0);
					if (empty($system_menu[$menu_name]['section'][$section_name]['menu'][$permission_name]['is_display'])) {
						$section_hidden_menu_count++;
					}
				}
				if (empty($section['is_display']) && $section_hidden_menu_count == count($section['menu']) && $section_name != 'platform_module') {
					$system_menu[$menu_name]['section'][$section_name]['is_display'] = 0;
				}
				$system_menu[$menu_name]['section'][$section_name]['menu'] = iarray_sort($system_menu[$menu_name]['section'][$section_name]['menu'], 'displayorder', 'desc');
			}
		}
		$add_top_nav = pdo_getall('core_menu', array('group_name' => 'frame', 'is_system <>' => 1), array('title', 'url', 'permission_name', 'displayorder', 'icon'));
		if (!empty($add_top_nav)) {
			foreach ($add_top_nav as $menu) {
				$menu['url'] = strexists($menu['url'], 'http') ?  $menu['url'] : $_W['siteroot'] . $menu['url'];
				$menu['blank'] = true;
				$menu['is_display'] = true;
				$system_menu[$menu['permission_name']] = $menu;
			}
		}
		$system_menu = iarray_sort($system_menu, 'displayorder', 'asc');
		cache_delete('system_frame');
		cache_write('system_frame', $system_menu);
		return $system_menu;
	}
}

function cache_build_module_subscribe_type() {
	global $_W;
	$modules = pdo_fetchall("SELECT name, subscribes FROM " . tablename('modules') . " WHERE subscribes <> ''");
	$subscribe = array();
	if (!empty($modules)) {
		foreach ($modules as $module) {
			$module['subscribes'] = unserialize($module['subscribes']);
			if (!empty($module['subscribes'])) {
				foreach ($module['subscribes'] as $event) {
					if ($event == 'text') {
						continue;
					}
					$subscribe[$event][] = $module['name'];
				}
			}
		}
	}

	$module_ban = $_W['setting']['module_receive_ban'];
	foreach ($subscribe as $event => $module_group) {
		if (!empty($module_group)) {
			foreach ($module_group as $index => $module) {
				if (!empty($module_ban[$module])) {
					unset($subscribe[$event][$index]);
				}
			}
		}
	}
	cache_write('module_receive_enable', $subscribe);
	return $subscribe;
}



function cache_build_cloud_ad() {
	global $_W;
	$uniacid_arr = pdo_fetchall("SELECT uniacid FROM " . tablename('uni_account'));
	foreach($uniacid_arr as $account){
		cache_delete("stat:todaylock:{$account['uniacid']}");
		cache_delete("cloud:ad:uniaccount:{$account['uniacid']}");
		cache_delete("cloud:ad:app:list:{$account['uniacid']}");
	}
	cache_delete("cloud:flow:master");
	cache_delete("cloud:ad:uniaccount:list");
	cache_delete("cloud:ad:tags");
	cache_delete("cloud:ad:type:list");
	cache_delete("cloud:ad:app:support:list");
	cache_delete("cloud:ad:site:finance");
}


function cache_build_uninstalled_module() {
	load()->model('cloud');
	load()->classs('cloudapi');
	load()->model('extension');
	load()->func('file');
	$cloud_api = new CloudApi();
	$cloud_m_count = $cloud_api->get('site', 'stat', array('module_quantity' => 1), 'json');
	$sql = 'SELECT * FROM '. tablename('modules') . " as a LEFT JOIN" . tablename('modules_recycle') . " as b ON a.name = b.modulename WHERE b.modulename is NULL";
	$installed_module = pdo_fetchall($sql, array(), 'name');

	$uninstallModules = array('recycle' => array(), 'uninstalled' => array());
	$recycle_modules = $cloud_api->post('cache', 'get', array('key' => cache_system_key('recycle_module:')));
	$recycle_modules = !empty($recycle_modules['data']) ? $recycle_modules['data'] : array();
	if (empty($recycle_modules)) {
		$recycle_modules = pdo_getall('modules_recycle', array(), array('modulename'), 'modulename');
		$cloud_api->post('cache', 'set', array('key' => cache_system_key('recycle_module:'), 'value' => $recycle_modules));
	}


//	$bought_module = cloud_m_bought();
	$bought_count_page = ceil(count($bought_module) / 200);
	for ($i = 0; $i < $bought_count_page; $i++) {
		$cloud_bought_module = array_slice($bought_module, $i * 200, 200);
		$cloud_module = cloud_m_query($cloud_bought_module);
		if (!empty($cloud_module) && !is_error($cloud_module)) {
			foreach ($cloud_module as $module) {
				$upgrade_support_module = false;
				$wxapp_support = !empty($module['site_branch']['wxapp_support']) && is_array($module['site_branch']['bought']) && in_array('wxapp', $module['site_branch']['bought']) ? $module['site_branch']['wxapp_support'] : 1;
				$app_support = !empty($module['site_branch']['app_support']) && is_array($module['site_branch']['bought']) && in_array('app', $module['site_branch']['bought']) ? $module['site_branch']['app_support'] : 1;
				$webapp_support = !empty($module['site_branch']['webapp_support']) && is_array($module['site_branch']['bought']) && in_array('webapp', $module['site_branch']['bought']) ? $module['site_branch']['webapp_support'] : MODULE_NOSUPPORT_WEBAPP;
				$welcome_support = !empty($module['site_branch']['system_welcome_support']) && is_array($module['site_branch']['bought']) && in_array('system_welcome', $module['site_branch']['bought']) ? $module['site_branch']['system_welcome_support'] : MODULE_NONSUPPORT_SYSTEMWELCOME;
				$phoneapp_support = !empty($module['site_branch']['phoneapp_support']) && is_array($module['site_branch']['bought']) && in_array('phoneapp', $module['site_branch']['bought']) ? $module['site_branch']['phoneapp_support'] : MODULE_NOSUPPORT_PHONEAPP;
				if ($wxapp_support ==  MODULE_NONSUPPORT_WXAPP && $app_support == MODULE_NONSUPPORT_ACCOUNT && $webapp_support == MODULE_NOSUPPORT_WEBAPP && $welcome_support == MODULE_NONSUPPORT_SYSTEMWELCOME && $phoneapp_support == MODULE_NOSUPPORT_PHONEAPP) {
					$app_support = MODULE_SUPPORT_ACCOUNT;
				}
				if (!empty($installed_module[$module['name']]) && ($installed_module[$module['name']]['app_support'] != $app_support || $installed_module[$module['name']]['wxapp_support'] != $wxapp_support || $installed_module[$module['name']]['webapp_support'] != $webapp_support || $installed_module[$module['name']]['welcome_support'] != $welcome_support || $installed_module[$module['name']]['phoneapp_support'] != $phoneapp_support)) {
					$upgrade_support_module = true;
				}
				if (!in_array($module['name'], array_keys($installed_module)) || $upgrade_support_module) {
					$status = !empty($recycle_modules[$module['name']]) ? 'recycle' : 'uninstalled';
					if (!empty($module['id'])) {
						$cloud_module_info = array (
							'from' => 'cloud',
							'name' => $module['name'],
							'version' => $module['version'],
							'title' => $module['title'],
							'thumb' => $module['thumb'],
							'wxapp_support' => $wxapp_support,
							'app_support' => $app_support,
							'webapp_support' => $webapp_support,
							'phoneapp_support' => $phoneapp_support,
							'welcome_support' => $welcome_support,
							'main_module' => empty($module['main_module']) ? '' : $module['main_module'],
							'upgrade_support' => $upgrade_support_module
						);
						if ($upgrade_support_module) {
							if ($wxapp_support == MODULE_SUPPORT_WXAPP && $installed_module[$module['name']]['wxapp_support'] != MODULE_SUPPORT_WXAPP) {
								$uninstall_modules[$status]['wxapp'][$module['name']] = $cloud_module_info;
							}
							if ($app_support == MODULE_SUPPORT_ACCOUNT && $installed_module[$module['name']]['app_support'] != MODULE_SUPPORT_ACCOUNT) {
								$uninstall_modules[$status]['app'][$module['name']] = $cloud_module_info;
							}
							if ($webapp_support == MODULE_SUPPORT_WEBAPP && $installed_module[$module['name']]['webapp_support'] != MODULE_SUPPORT_WEBAPP) {
								$uninstall_modules[$status]['webapp'][$module['name']] = $cloud_module_info;
							}
							if ($phoneapp_support == MODULE_SUPPORT_PHONEAPP && $installed_module[$module['name']]['phoneapp_support'] != MODULE_SUPPORT_PHONEAPP) {
								$uninstall_modules[$status]['phoneapp'][$module['name']] = $cloud_module_info;
							}
							if ($welcome_support == MODULE_SUPPORT_SYSTEMWELCOME && $installed_module[$module['name']]['welcome_support'] != MODULE_SUPPORT_SYSTEMWELCOME) {
								$uninstall_modules[$status]['system_welcome'][$module['name']] = $cloud_module_info;
							}
						} else {
							if ($wxapp_support == MODULE_SUPPORT_WXAPP) {
								$uninstall_modules[$status]['wxapp'][$module['name']] = $cloud_module_info;
							}
							if ($app_support == MODULE_SUPPORT_WXAPP) {
								$uninstall_modules[$status]['app'][$module['name']] = $cloud_module_info;
							}
							if ($webapp_support == MODULE_SUPPORT_WEBAPP) {
								$uninstall_modules[$status]['webapp'][$module['name']] = $cloud_module_info;
							}
							if ($phoneapp_support == MODULE_SUPPORT_PHONEAPP) {
								$uninstall_modules[$status]['phoneapp'][$module['name']] = $cloud_module_info;
							}
							if ($welcome_support == MODULE_SUPPORT_SYSTEMWELCOME) {
								$uninstall_modules[$status]['system_welcome'][$module['name']] = $cloud_module_info;
							}
						}
					}
				}
			}
		}
	}
	$path = IA_ROOT . '/addons/';
	mkdirs($path);

	$module_file = glob($path . '*');
	if (is_array($module_file) && !empty($module_file)) {
		foreach ($module_file as $modulepath) {
			if (!is_dir($modulepath)) {
				continue;
			}
			$upgrade_support_module = false;
			$modulepath = str_replace($path, '', $modulepath);
			$manifest = ext_module_manifest($modulepath);
			if (!is_array($manifest) || empty($manifest) || empty($manifest['application']['identifie'])) {
				continue;
			}
			$main_module = empty($manifest['platform']['main_module']) ? '' : $manifest['platform']['main_module'];
			$manifest = ext_module_convert($manifest);
			if (!empty($installed_module[$modulepath]) && ($manifest['app_support'] != $installed_module[$modulepath]['app_support'] || $manifest['wxapp_support'] != $installed_module[$modulepath]['wxapp_support'] || $manifest['welcome_support'] != $installed_module[$modulepath]['welcome_support'] || $manifest['phoneapp_support'] != $installed_module[$modulepath]['phoneapp_support'])) {
				$upgrade_support_module = true;
			}
			if (!in_array($manifest['name'], array_keys($installed_module)) || $upgrade_support_module) {
				$module[$manifest['name']] = $manifest;
				$module_info = array(
					'from' => 'local',
					'name' => $manifest['name'],
					'version' => $manifest['version'],
					'title' => $manifest['title'],
					'app_support' => $manifest['app_support'],
					'wxapp_support' => $manifest['wxapp_support'],
					'webapp_support' => $manifest['webapp_support'],
					'phoneapp_support' => $manifest['phoneapp_support'],
					'welcome_support' => $manifest['welcome_support'],
					'main_module' => $main_module,
					'upgrade_support' => $upgrade_support_module
				);
				$module_type = !empty($recycle_modules[$manifest['name']]) ? 'recycle' : 'uninstalled';
				if ($upgrade_support_module) {
					if ($module_info['app_support'] == 2 && $installed_module[$module_info['name']]['app_support'] != 2) {
						$uninstall_modules['uninstalled']['app'][$manifest['name']] = $module_info;
					}
					if ($module_info['wxapp_support'] == 2 && $installed_module[$module_info['name']]['wxapp_support'] != 2) {
						$uninstall_modules['uninstalled']['wxapp'][$manifest['name']] = $module_info;
					}
					if ($module_info['webapp_support'] == MODULE_SUPPORT_WEBAPP && $installed_module[$module_info['name']]['webapp_support'] != MODULE_SUPPORT_WEBAPP) {
						$uninstall_modules['uninstalled']['webapp'][$manifest['name']] = $module_info;
					}
					if ($module_info['phoneapp_support'] == MODULE_SUPPORT_PHONEAPP && $installed_module[$module_info['name']]['phoneapp_support'] != MODULE_SUPPORT_PHONEAPP) {
						$uninstall_modules['uninstalled']['phoneapp'][$manifest['name']] = $module_info;
					}
					if ($module_info['welcome_support'] == MODULE_SUPPORT_SYSTEMWELCOME && $installed_module[$module_info['name']]['welcome_support'] != MODULE_SUPPORT_SYSTEMWELCOME) {
						$uninstall_modules['uninstalled']['system_welcome'][$manifest['name']] = $module_info;
					}
				} else {
					if ($module_info['app_support'] == 2) {
						$uninstall_modules[$module_type]['app'][$manifest['name']] = $module_info;
					}
					if ($module_info['wxapp_support'] == 2) {
						$uninstall_modules[$module_type]['wxapp'][$manifest['name']] = $module_info;
					}
					if ($module_info['webapp_support'] == MODULE_SUPPORT_WEBAPP) {
						$uninstall_modules[$module_type]['webapp'][$manifest['name']] = $module_info;
					}
					if ($module_info['phoneapp_support'] == MODULE_SUPPORT_PHONEAPP) {
						$uninstall_modules[$module_type]['phoneapp'][$manifest['name']] = $module_info;
					}
					if ($module_info['welcome_support'] == MODULE_SUPPORT_SYSTEMWELCOME) {
						$uninstall_modules[$module_type]['system_welcome'][$manifest['name']] = $module_info;
					}
				}
			}
		}
	}
	$cache = array(
		'cloud_m_count' => $cloud_m_count['module_quantity'],
		'modules' => $uninstall_modules,
		'app_count' => count($uninstall_modules['uninstalled']['app']),
		'wxapp_count' => count($uninstall_modules['uninstalled']['wxapp']),
		'webapp_count' => count($uninstall_modules['uninstalled']['webapp']),
		'phoneapp_count' => count($uninstall_modules['uninstalled']['phoneapp']),
		'welcome_count' => count($uninstall_modules['uninstalled']['system_welcome'])
	);
	cache_write(cache_system_key('module:all_uninstall'), $cache, CACHE_EXPIRE_LONG);
	return $cache;
}


function cache_build_proxy_wechatpay_account() {
	global $_W;
	load()->model('account');
	if(empty($_W['isfounder'])) {
		$where = " WHERE `uniacid` IN (SELECT `uniacid` FROM " . tablename('uni_account_users') . " WHERE `uid`=:uid)";
		$params[':uid'] = $_W['uid'];
	}
	$sql = "SELECT * FROM " . tablename('uni_account') . $where;
	$uniaccounts = pdo_fetchall($sql, $params);
	$service = array();
	$borrow = array();
	if (!empty($uniaccounts)) {
		foreach ($uniaccounts as $uniaccount) {
			$account = account_fetch($uniaccount['default_acid']);
			$account_setting = pdo_get('uni_settings', array ('uniacid' => $account['uniacid']));
			$payment = iunserializer($account_setting['payment']);
			if (is_array($account) && !empty($account['key']) && !empty($account['secret']) && in_array($account['level'], array (4)) &&
				is_array($payment) && !empty($payment) && intval($payment['wechat']['switch']) == 1) {

				if ((!is_bool ($payment['wechat']['switch']) && $payment['wechat']['switch'] != 4) || (is_bool ($payment['wechat']['switch']) && !empty($payment['wechat']['switch']))) {
					$borrow[$account['uniacid']] = $account['name'];
				}
			}
			if (!empty($payment['wechat_facilitator']['switch'])) {
				$service[$account['uniacid']] = $account['name'];
			}
		}
	}
	$cache = array(
		'service' => $service,
		'borrow' => $borrow
	);
	cache_write(cache_system_key("proxy_wechatpay_account:"), $cache);
	return $cache;
}


function cache_build_module_info($module_name) {
	global $_W;
	cache_delete(cache_system_key(CACHE_KEY_MODULE_INFO, $module_name));
	cache_delete(cache_system_key(CACHE_KEY_MODULE_SETTING, $_W['uniacid'], $module_name));
}


function cache_build_uni_group() {
	cache_delete(cache_system_key(CACHE_KEY_UNI_GROUP));
}


function cache_build_cloud_upgrade_module() {
	load()->model('cloud');
	load()->model('extension');

	$module_list = pdo_getall('modules', array(), array(), 'name');
	$cloud_module = cloud_m_query();
	$modules = array();
	if (is_array($module_list) && !empty($module_list)) {
		foreach ($module_list as $module) {
			if (in_array($module['name'], array_keys($cloud_module))) {
				$cloud_m_info = $cloud_module[$module['name']];
				$module['site_branch'] = $cloud_m_info['site_branch']['id'];
				if (empty($module['site_branch'])) {
					$module['site_branch'] = $cloud_m_info['branch'];
				}
				$cloud_branch_version = $cloud_m_info['branches'][$module['site_branch']]['version'];
				if (!empty($cloud_m_info['branches'])) {
					$best_branch_id = 0;
					foreach ($cloud_m_info['branches'] as $branch) {
						if (empty($branch['status']) || empty($branch['show'])) {
							continue;
						}
						if ($best_branch_id == 0) {
							$best_branch_id = $branch['id'];
						} else {
							if ($branch['displayorder'] > $cloud_m_info['branches'][$best_branch_id]['displayorder']) {
								$best_branch_id = $branch['id'];
							}
						}
					}
				} else {
					continue;
				}
				$module['branches'] = $cloud_m_info['branches'];
				$best_branch = $cloud_m_info['branches'][$best_branch_id];
				$module['from'] = 'cloud';
				if (version_compare($module['version'], $cloud_branch_version) == -1) {
					$module['upgrade_branch'] = true;
					$module['upgrade'] = true;
				}
				if ($cloud_m_info['displayorder'] < $best_branch['displayorder']) {
					$module['new_branch'] = true;
					$module['upgrade'] = true;
				}
				if ($module['upgrade']) {
					$modules[$module['name']] = $module;
				}
			}
		}
	} else {
		return array();
	}
	cache_write(cache_system_key('all_cloud_upgrade_module:'), $modules, 1800);
	return $modules;
}
