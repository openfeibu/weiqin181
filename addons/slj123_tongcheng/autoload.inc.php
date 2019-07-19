<?PHP defined('IN_IA') || die('Access Denied');

function ModuleNamePascal() {
    return preg_replace_callback('/_([a-z])/', function($matches) {
        return strtoupper($matches[1]);
    }, ucfirst(basename(dirname(__FILE__))));
}

function ModuleName() {
    return basename(dirname(__FILE__));
}


function tpl_form_field_district_modify($name, $values = array()) {
    $html = '';
    if (!defined('TPL_INIT_DISTRICT')) {
        $html .= '
		<script type="text/javascript">
			require(["../../../../addons/yb_wxcard/static/js/district"], function(dis){
				$(".tpl-district-container").each(function(){
					var elms = {};
					elms.province = $(this).find(".tpl-province")[0];
					elms.city = $(this).find(".tpl-city")[0];
					elms.district = $(this).find(".tpl-district")[0];
					var vals = {};
					vals.province = $(elms.province).attr("data-value");
					vals.city = $(elms.city).attr("data-value");
					vals.district = $(elms.district).attr("data-value");
					dis.render(elms, vals, {withTitle: true});
				});
			});
		</script>';
        define('TPL_INIT_DISTRICT', true);
    }
    if (empty($values) || !is_array($values)) {
        $values = array('province'=>'','city'=>'','district'=>'');
    }
    if(empty($values['province'])) {
        $values['province'] = '';
    }
    if(empty($values['city'])) {
        $values['city'] = '';
    }
    if(empty($values['district'])) {
        $values['district'] = '';
    }
    $html .= '
		<div class="row row-fix tpl-district-container">
			<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
				<select name="' . $name . '[province]" data-value="' . $values['province'] . '" class="form-control tpl-province">
				</select>
			</div>
			<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
				<select name="' . $name . '[city]" data-value="' . $values['city'] . '" class="form-control tpl-city">
				</select>
			</div>
			<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
				<select name="' . $name . '[district]" data-value="' . $values['district'] . '" class="form-control tpl-district">
				</select>
			</div>
		</div>';
    return $html;
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////
spl_autoload_register(function($class) {
    preg_match('/(.+)API$/', $class, $matches);
    $file = IA_ROOT . '/api/' . $matches[1] . '/' . $class . '.api.php';
    if(is_file($file)) include $file;
});
spl_autoload_register(function($class) {
    preg_match('/(.+)API$/', $class, $matches);
    $file = IA_ROOT . '/addons/' . ModuleName() . '/api/' . $matches[1] . '/' . $class . '.api.php';
    if(is_file($file)) include $file;
});
spl_autoload_register(function($class) {
    $file = IA_ROOT . '/' . dirname(str_replace('\\', '/', $class)) . '.php';
    if(is_file($file)) include $file;
});
spl_autoload_register(function($class) {
    $file = IA_ROOT . '/addons/' . ModuleName() . '/' . dirname(str_replace('\\', '/', $class)) . '.php';
    if(is_file($file)) include $file;
});
spl_autoload_register(function($class) {
    preg_match('/_(.+)_/', preg_replace_callback('/[A-Z]/', function($matches) {
        return '_' . strtolower($matches[0]);
    }, $class), $matches);
    $file = IA_ROOT . '/addons/' . $matches[1] . '/classes/' . $class . '.class.php';
    if(is_file($file)) include $file;
});
spl_autoload_register(function($class) {
    $addon = substr($class, 0, strpos($class, '\\'));
    $path = substr(str_replace('\\', '/', $class), strpos($class, '\\') + 1);
    $base = basename($path);
    preg_match('/_(.+)/', preg_replace_callback('/[A-Z]/', function($matches) {
        return '_' . strtolower($matches[0]);
    }, $addon), $matches);
    $file = IA_ROOT . '/addons/' . $matches[1] . '/' . $path . '/' . $base . '.php';
//    error_log('$base:' . var_export($base, !0) . "\n", 3, dirname(__FILE__) . '/error_log.log');
//    error_log('$addon:' . var_export($addon, !0) . "\n", 3, dirname(__FILE__) . '/error_log.log');
//    error_log('$path:' . var_export($path, !0) . "\n", 3, dirname(__FILE__) . '/error_log.log');
//    error_log('$class:' . var_export($class, !0) . "\n", 3, dirname(__FILE__) . '/error_log.log');
//    error_log('$file:' . var_export($file, !0) . "\n", 3, dirname(__FILE__) . '/error_log.log');
    if(is_file($file)) include $file;
});
spl_autoload_register(function($class) {
    $addon = substr($class, 0, strpos($class, '\\'));
    $path = substr(str_replace('\\', '/', $class), strpos($class, '\\') + 1);
    preg_match('/_(.+)/', preg_replace_callback('/[A-Z]/', function($matches) {
        return '_' . strtolower($matches[0]);
    }, $addon), $matches);
    $file = IA_ROOT . '/addons/' . $matches[1] . '/classes/' . $path . '.php';
    if(is_file($file)) include $file;
});
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
include dirname(__FILE__) . '/error_handle.php';

define('AUTOLOAD_VERSION', '2018年3月1日 16点35分');
