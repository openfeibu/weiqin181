<?PHP namespace Slj123Tongcheng\model; defined('IN_IA') || die('Access Denied');

class Member
{
    public function __construct()
    {
    }
    public static function insertNewFans(
        $agentid = 0,
        $agentid2 = 0,
        $agentid3 = 0,
        $username = '',
        $mobile = '',
        $address = '',
        $sex = null,
        $country = '',
        $province = '',
        $city = '',
        $area = '',
        $lat = null,
        $lng = null,
        $totalprice = 0,
        $avgprice = 0,
        $totalcount = 0,
        $paytime = 0,
        $status = null,
        $noticetime = 0,
        $lasttime = 0,
        $is_commission = null,
        $commission_price = 0,
        $delivery_price = 0,
        $checked_market_id = 0,
        $headimgurl = null,
        $nickname = null
    ) {
        //\WeUtility::logging('$fans', var_export($fans, !0) . "\n");
        $uid = self::getFirstUID();
        $data = array(
            'uid' => $uid,
            'weid' => $GLOBALS['_W']['uniacid'],
            'from_user' => $GLOBALS['_W']['fans']['openid'],
        );
        isset($nickname) && ($data['nickname'] = trim($nickname));
        isset($headimgurl) && ($data['headimgurl'] = trim($headimgurl));
        intval($agentid) && ($data['agentid'] = intval($agentid));
        intval($agentid2) && ($data['agentid2'] = intval($agentid2));
        intval($agentid3) && ($data['agentid3'] = intval($agentid3));
        trim($username) && ($data['username'] = trim($username));
        trim($mobile) && ($data['mobile'] = trim($mobile));
        trim($address) && ($data['address'] = trim($address));
        isset($sex) && ($data['sex'] = intval($sex));
        trim($country) && ($data['country'] = trim($country));
        trim($province) && ($data['province'] = trim($province));
        trim($city) && ($data['city'] = trim($city));
        trim($area) && ($data['area'] = trim($area));
        isset($lat) && ($data['lat'] = floatval($lat));
        isset($lng) && ($data['lng'] = floatval($lng));
        floatval($totalprice) && ($data['totalprice'] = floatval($totalprice));
        floatval($avgprice) && ($data['avgprice'] = floatval($avgprice));
        intval($totalcount) && ($data['totalcount'] = intval($totalcount));
        intval($paytime) && ($data['paytime'] = intval($paytime));
        isset($status) && ($data['status'] = intval($status));
        intval($noticetime) && ($data['noticetime'] = intval($noticetime));
        intval($lasttime) && ($data['lasttime'] = intval($lasttime));
        isset($is_commission) && ($data['is_commission'] = intval($is_commission));
        floatval($commission_price) && ($data['commission_price'] = floatval($commission_price));
        floatval($delivery_price) && ($data['delivery_price'] = floatval($delivery_price));
        intval($checked_market_id) && ($data['checked_market_id'] = intval($checked_market_id));
        return SQL::insertOrUpdate('fans', $data);
    }
    public static function getFirstUID()
    {
        return SQL::column("
SELECT `uid`
FROM `@#__U_mc_mapping_fans`
WHERE `openid` = :openid
ORDER BY `uid` ASC
LIMIT 1", array( ':openid' => $GLOBALS['_W']['fans']['openid'] ));
    }
    public static function getActiveMarketID()
    {
        return SQL::column("
SELECT `checked_market_id`
FROM `@#__M_fans`
WHERE `from_user` = :openid ", array( ':openid' => $GLOBALS['_W']['fans']['openid'] ));
    }
    public static function getFans($Openid = '') {
        empty($Openid) && ($Openid = $GLOBALS['_W']['fans']['openid']);
        return SQL::one("
SELECT *
FROM `@#__M_fans`
WHERE `from_user` = :openid", array( ':openid' => $Openid));
    }
}
