<?PHP defined('IN_IA') || die('Access Denied');

include_once dirname(__FILE__) . '/autoload.inc.php';

class Slj123_tongchengModule extends WeModule
{

    public function settingsDisplay($settings)
    {
        if (checksubmit()) {
            $settings = array_merge($settings, $GLOBALS['_GPC']['settings']);
            if ($this->saveSettings($settings)) {
                message('保存参数成功', 'refresh');
            }
        }
        include $this->template('settings');
    }

}
