<?PHP namespace Slj123Tongcheng\model; defined('IN_IA') || die('Access Denied');

class Pay extends Base
{
    public function __construct()
    {
    }
    public static function WeChat($chinnel)
    {
        Payment::buildwechat($GLOBALS['_GPC']['ProductID']['$TotalPrice'], $GLOBALS['_GPC']['ProductID']['$MarketOrderSN'], $chinnel, 0, 5, 0, 0, 0, $GLOBALS['_GPC']['ProductID']['$MarketOrderSN']);
    }
    public static function payResult($params)
    {
        \WeUtility::logging('Pay::payResult', var_export($params, true));
        return '';
    }
}
