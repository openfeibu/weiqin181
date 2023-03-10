<?PHP namespace Slj123Tongcheng\model; defined('IN_IA') || die('Access Denied');

class Payment extends Base
{
    public function __construct()
    {
    }
    public static function wechat_build($params, $wechat, $WechatBuildChannelID = 0) {
        $ModuleName = self::ModuleName();
        load()->func('communication');
        if(empty($wechat['version']) && !empty($wechat['signkey'])) {
            $wechat['version'] = 1;
        }
        $wOpt = array();
        if($wechat['version'] == 1) {
            $wOpt['appId']               = $wechat['appid'];
            $wOpt['timeStamp']           = TIMESTAMP . "";
            $wOpt['nonceStr']            = random(8);
            $package                     = array();
            $package['bank_type']        = 'WX';
            $package['body']             = urlencode($params['title']);
            $package['attach']           = $GLOBALS['_W']['uniacid'] . ':' . $WechatBuildChannelID;
            $package['partner']          = $wechat['partner'];
            $package['device_info']      = $ModuleName;
            $package['out_trade_no']     = $params['uniontid'];
            $package['total_fee']        = $params['fee'] * 100;
            $package['fee_type']         = '1';
            $package['notify_url']       = $GLOBALS['_W']['siteroot'] . "addons/{$ModuleName}/payment/wechat/notify.php";
            $package['spbill_create_ip'] = CLIENT_IP;
            $package['time_start']       = date('YmdHis', TIMESTAMP);
            $package['time_expire']      = date('YmdHis', TIMESTAMP + 600);
            $package['input_charset']    = 'UTF-8';
            ksort($package);
            $string1 = '';
            foreach($package as $key => $v) {
                if(empty($v)) {
                    continue;
                }
                $string1 .= "{$key}={$v}&";
            }
            $string1 .= "key={$wechat['key']}";
            $sign    = strtoupper(md5($string1));
            $string2 = '';
            foreach($package as $key => $v) {
                $v = urlencode($v);
                $string2 .= "{$key}={$v}&";
            }
            $string2 .= "sign={$sign}";
            $wOpt['package'] = $string2;
            $string          = '';
            $keys            = array(
                'appId',
                'timeStamp',
                'nonceStr',
                'package',
                'appKey',
            );
            sort($keys);
            foreach($keys as $key) {
                $v = $wOpt[$key];
                if($key == 'appKey') {
                    $v = $wechat['signkey'];
                }
                $key = strtolower($key);
                $string .= "{$key}={$v}&";
            }
            $string           = rtrim($string, '&');
            $wOpt['signType'] = 'SHA1';
            $wOpt['paySign']  = sha1($string);
            return $wOpt;
        } else {
            $package                     = array();
            $package['appid']            = $wechat['appid'];
            $package['mch_id']           = $wechat['mchid'];
            $package['nonce_str']        = random(8);
            $package['body']             = $params['title'];
            $package['device_info']      = $ModuleName;
            $package['attach']           = $GLOBALS['_W']['uniacid'] . ':' . $WechatBuildChannelID;
            $package['out_trade_no']     = $params['uniontid'];
            $package['total_fee']        = $params['fee'] * 100;
            $package['spbill_create_ip'] = CLIENT_IP;
            $package['time_start']       = date('YmdHis', TIMESTAMP);
            $package['time_expire']      = date('YmdHis', TIMESTAMP + 600);
            $package['notify_url']       = $GLOBALS['_W']['siteroot'] . "addons/{$ModuleName}/payment/wechat/notify.php";
            $package['trade_type']       = 'JSAPI';
            $package['openid']           = $GLOBALS['_W']['fans']['from_user'];
            ksort($package, SORT_STRING);
            $string1 = '';
            foreach($package as $key => $v) {
                if(empty($v)) {
                    continue;
                }
                $string1 .= "{$key}={$v}&";
            }
            $string1 .= "key={$wechat['signkey']}";
            $package['sign'] = strtoupper(md5($string1));
            $dat             = array2xml($package);
            $response        = ihttp_request('https://api.mch.weixin.qq.com/pay/unifiedorder', $dat);
            if(is_error($response)) {
                return $response;
            }
            $xml = @simplexml_load_string($response['content'], 'SimpleXMLElement', LIBXML_NOCDATA);
            if(strval($xml->return_code) == 'FAIL') {
                return error(-1, strval($xml->return_msg));
            }
            if(strval($xml->result_code) == 'FAIL') {
                return error(-1, strval($xml->err_code) . ': ' . strval($xml->err_code_des));
            }
            $prepayid          = $xml->prepay_id;
            $wOpt['appId']     = $wechat['appid'];
            $wOpt['timeStamp'] = TIMESTAMP;
            $wOpt['nonceStr']  = random(8);
            $wOpt['package']   = 'prepay_id=' . $prepayid;
            $wOpt['signType']  = 'MD5';
            ksort($wOpt, SORT_STRING);
            $string = '';
            foreach($wOpt as $key => $v) {
                $string .= "{$key}={$v}&";
            }
            $string .= "key={$wechat['signkey']}";
            $wOpt['paySign'] = strtoupper(md5($string));
            return $wOpt;
        }
    }
    public static function buildwechat(
        $Fee
        , $Title
        , $WechatBuildChannelID = 0
        , $ObjectID = 0 //?????????id
        , $Type = 4 // 1?????? 2?????? 3?????? 4?????? 5????????????
        , $StoreID = 0 //??????ID
        , $Score = 0 //????????????
        , $PasswordID = 0 // ?????????ID
        , $PayNo = null // ????????????????????????
    ) {
        $ModuleName   = self::ModuleName();
        $logno = empty($PayNo) ? Util::createNO('core_paylog', 'uniontid', 'RC') : $PayNo;
        self::TestAndCreateBillLogTable();
        pdo_insert($ModuleName . '_bill_log', array(
            'uniacid'       => $GLOBALS['_W']['uniacid'],
            'openid'        => $GLOBALS['_W']['fans']['openid'],
            'title'         => $Title,
            'type'          => $Type,// 1?????? 2?????? 3?????? 4?????? 5????????????
            'payment'       => 3,// ???????????? 1??????????????? 2??????????????? 3???????????????
            'passwordid'    => $PasswordID,// ?????????ID
            'operationtype' => 1,//0?????? 1??????
            'storeid'       => $StoreID,
            'tid'           => $logno,
            'objectid'      => $ObjectID,//?????????id
            'money'         => $Fee,
            'score'         => $Score,
            'dateline'      => TIMESTAMP,
        ));
        pdo_insert('core_paylog', array(
            'type'       => 'wechat',
            'uniacid'    => $GLOBALS['_W']['uniacid'],
            'acid'       => $GLOBALS['_W']['acid'],
            'openid'     => $GLOBALS['_W']['fans']['openid'], //$GLOBALS['_W']['member']['uid'],
            'module'     => $ModuleName, //????????????????????????$this??????
            'uniontid'   => $logno,
            'tid'        => $logno,
            'fee'        => $Fee,
            'card_fee'   => $Fee,
            'status'     => '0',
            'is_usecard' => '0',
        ));
        $setting = uni_setting($GLOBALS['_W']['uniacid'], array(
            'payment',
        ));
        $wechat  = array(
            'success' => false,
        );
        $params  = array(
            'uniontid' => $logno,
            'tid'      => $logno,
            'user'     => $GLOBALS['_W']['fans']['openid'],
            'fee'      => $Fee,
            'title'    => $Title,
        );
        //load()->model('payment');
        if(is_array($setting['payment'])) {
            $options           = $setting['payment']['wechat'];
            $options['appid']  = $GLOBALS['_W']['account']['key'];
            $options['secret'] = $GLOBALS['_W']['account']['secret'];
            $wechat            = self::wechat_build($params, $options, $WechatBuildChannelID);
            $wechat['success'] = false;
            if(!is_error($wechat)) {
                $wechat['success'] = true;
            } else {
                Util::diejson(0, $wechat['message']);
            }
        }
        if(!$wechat['success']) {
            Util::diejson(0, '????????????????????????!');
        }
        $wechat['timeStamp'] = "{$wechat['timeStamp']}";
        Util::diejson(1, '[OK]', array(
            'wechat' => $wechat,
        ));
    }
    public static function PaymentWechatJSOutput() {
        $HTML = <<<HTML
<script type="text/javascript">//<!--<!CDATA[[  //?????????
function buildwechat(ProductID,count,order_type, successCallback, failCallback) {
	$.ajax({
		type: "POST",
		data: { "Method": "WeChat", "Action": "Pay", "ProductID": ProductID,"count": count,"order_type": order_type  },
		dataType: "json",  // ??????Ajax????????????
		beforeSend: function() {
			$("#wxpay").html('?????????...');
		}, success: function(json) {
			//console.log(json);
			//alert(JSON.stringify(json));
			var a = document.createElement('a'), disabled_pay = '';
			for(var ii in json.disabled_pay) {
				isNaN(ii - 0) || (disabled_pay += '&disabled_pay[]=' + json.disabled_pay[ii]);
			}
		/*	a.href = "/index.php?i={$GLOBALS['_W']['uniacid']}&c=entry&do=pay&m=recreational_vehicle&orderid=" + json.order_id + disabled_pay;
			a.click();
			return;*/
			if(typeof WeixinJSBridge == "undefined") {
				document.addEventListener && document.addEventListener('WeixinJSBridgeReady', jsApiCall(json, successCallback, failCallback), false);
				document.attachEvent && (document.attachEvent('WeixinJSBridgeReady', jsApiCall(json, successCallback, failCallback)), true) &&
				document.attachEvent('onWeixinJSBridgeReady', jsApiCall(json, successCallback, failCallback));
			} else {
				jsApiCall(json, successCallback, failCallback);
			}
		}
	});
}
function jsApiCall(obj, successCallback, failCallback) {
	var callback = {
		'get_brand_wcpay_request:cancel': function(res) {
			$('#wxpay').removeAttr('disabled').html('????????????');
		}, 'get_brand_wcpay_request:fail': function(res) {
			if(res.err_desc.indexOf('????????????')) {
				qr();
			} else {
				alert("????????????" + res.err_desc);
				$('#wxpay').removeAttr('disabled').html('????????????');
			}
		}, 'get_brand_wcpay_request:ok': function(res) {
			('function' === typeof successCallback) && successCallback.apply(window, [res]);
		}, 'default': function(res) {
			alert("????????????" + res.error_msg);
			$('#wxpay').removeAttr('disabled').html('????????????');
		}
	};
	(obj.status == 1) ?
	WeixinJSBridge.invoke('getBrandWCPayRequest', obj.result.wechat, function(res) {
		callback[res.err_msg] ? callback[res.err_msg](res) : callback['default'](res);
	}) : (function() {
		alert(JSON.stringify(obj));
		if(obj.res.indexOf('????????????') > 0) {
			$("#editphone").trigger("click");
		}
	})();
}
//]]>--></script>
HTML;
        echo $HTML;
    }
    private static function TestAndCreateBillLogTable() {
        $SQL = <<<SQL
CREATE TABLE `ims_bill_log` (
  `id` INT(10) NOT NULL AUTO_INCREMENT,
  `uniacid` INT(10) NOT NULL DEFAULT '0' COMMENT '?????????id',
  `openid` VARCHAR(50) NOT NULL COMMENT '??????id',
  `title` VARCHAR(200) NOT NULL COMMENT '??????',
  `type` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '????????????-??????:1,??????:2',
  `passwordid` INT(10) NOT NULL DEFAULT '0' COMMENT '?????????id',
  `payment` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '????????????',
  `operationtype` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '?????????????????? ??????:1  ??????:0',
  `objectid` INT(10) NOT NULL COMMENT '??????id',
  `tid` VARCHAR(60) NOT NULL DEFAULT '',
  `storeid` INT(10) NOT NULL DEFAULT '0' COMMENT '??????id',
  `money` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT '????????????',
  `score` INT(10) NOT NULL DEFAULT '0' COMMENT '????????????',
  `dateline` INT(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SQL;
        $ModuleName = self::ModuleName();
        pdo_tableexists($ModuleName . '_bill_log') ||
        pdo_query(str_replace('`ims_bill_log`', tablename($ModuleName . '_bill_log'), $SQL));
    }
}
