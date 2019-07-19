<?PHP

class WeChatAPI {
    public static $version = '20171220104448';
    private static $limit = array(
        'temp' => array(
            'image' => array(
                'ext' => array('jpg', 'logo'),
                'size' => '1024 * 1024',
                'errmsg' => '临时图片只支持jpg/logo格式,大小不超过为1M',
            ),
            'voice' => array(
                'ext' => array('amr', 'mp3'),
                'size' => '2048 * 1024',
                'errmsg' => '临时语音只支持amr/mp3格式,大小不超过为2M',
            ),
            'video' => array(
                'ext' => array('mp4'),
                'size' => '10240 * 1024',
                'errmsg' => '临时视频只支持mp4格式,大小不超过为10M',
            ),
            'thumb' => array(
                'ext' => array('jpg', 'logo'),
                'size' => '64 * 1024',
                'errmsg' => '临时缩略图只支持jpg/logo格式,大小不超过为64K',
            ),
        ),
        'perm' => array(
            'image' => array(
                'ext' => array('bmp', 'png', 'jpeg', 'jpg', 'gif'),
                'size' => '2048 * 1024',
                'max' => 5000,
                'errmsg' => '永久图片只支持bmp/png/jpeg/jpg/gif格式,大小不超过为2M',
            ),
            'voice' => array(
                'ext' => array('amr', 'mp3', 'wma', 'wav', 'amr'),
                'size' => '5120 * 1024',
                'max' => 1000,
                'errmsg' => '永久语音只支持mp3/wma/wav/amr格式,大小不超过为5M,长度不超过60秒',
            ),
            'video' => array(
                'ext' => array('rm', 'rmvb', 'wmv', 'avi', 'mpg', 'mpeg', 'mp4'),
                'size' => '10240 * 1024 * 2',
                'max' => 1000,
                'errmsg' => '永久视频只支持rm/rmvb/wmv/avi/mpg/mpeg/mp4格式,大小不超过为20M',
            ),
            'thumb' => array(
                'ext' => array('bmp', 'png', 'jpeg', 'jpg', 'gif'),
                'size' => '2048 * 1024',
                'max' => 5000,
                'errmsg' => '永久缩略图只支持bmp/png/jpeg/jpg/gif格式,大小不超过为2M',
            ),

        ),
        'file_upload' => array(
            'image' => array(
                'ext' => array('jpg'),
                'size' => '1024 * 1024',
                'max' => -1,
                'errmsg' => '图片只支持jpg格式,大小不超过为1M',
            )
        )
    );
    private static $apis = array(
        'temp' => array(
            'add' => 'https://api.weixin.qq.com/cgi-bin/media/upload',
            'get' => 'https://api.weixin.qq.com/cgi-bin/media/get',
            'post_key' => 'media'
        ),
        'perm' => array(
            'add' => 'https://api.weixin.qq.com/cgi-bin/material/add_material',
            'get' => 'https://api.weixin.qq.com/cgi-bin/material/get_material',
            'del' => 'https://api.weixin.qq.com/cgi-bin/material/del_material',
            'count' => 'https://api.weixin.qq.com/cgi-bin/material/get_materialcount',
            'batchget' => 'https://api.weixin.qq.com/cgi-bin/material/batchget_material',
            'post_key' => 'media',
        ),
        'file_upload' => array(
            'add' => 'https://api.weixin.qq.com/cgi-bin/media/uploadimg',
            'post_key' => 'buffer',
        )
    );
    private static $result = array(
        'error' => 1,
        'message' => '',
        'data' => ''
    );
    public static function download($media_id, $mode, $fileName) {
        load()->model('account');
        $acc = WeAccount::create($GLOBALS['_W']['acid']);
        $token = $acc->getAccessToken();
        $modes = array_keys(static::$apis);
        array_pop($modes);
        in_array($mode, $modes) || ($mode = reset($modes));
        $fetchAPI = static::$apis[$mode]['get'] . "?access_token={$token}";
        $params = array(
            'media_id' => $media_id,
        );
        if('temp' === $mode) {
            $fetchAPI .= '&' . http_build_query($params);
            $res = \HttpRequestAPI::ihttp_request($fetchAPI);
        } else{
            $params = json_encode($params);
            $res = \HttpRequestAPI::ihttp_request($fetchAPI, $params);
        }
        $file = ATTACHMENT_ROOT . 'audios/' . $GLOBALS['_W']['uniacid'] . '/'.date('Y/m/') . $fileName . '.amr';
        load()->func('file');
        mkdirs(dirname($file));
        file_put_contents($file, $res['content']);
        return array(
            'file' => 'audios/' . $GLOBALS['_W']['uniacid'] . '/'.date('Y/m/') . $fileName . '.amr',
            //'attachurl' => $GLOBALS['_W']['attachurl'],
            //'attachurl_local' => $GLOBALS['_W']['attachurl_local'],
            //'attachurl_remote' => $GLOBALS['_W']['attachurl_remote'],
            //'siteroot' => $GLOBALS['_W']['siteroot'],
        );
    }
    public static function upload() {}


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private static $OAuth2URL = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect';
    private static $getAuthorizationCodeURL = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=APPID&secret=APPSECRET&code=CODE&grant_type=authorization_code';
    private static $userInfoURL = 'https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN';
    private static $scope = array('snsapi_base', 'snsapi_userinfo');
    public static function OAuth2($SCOPE = 0, $STATE = 0)
    {
        if (empty($GLOBALS['_GPC']['code'])) {
            $URL = (
                (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on') ||
                (isset($_SERVER['HTTP_FROM_HTTPS']) && strtolower($_SERVER['HTTP_FROM_HTTPS']) === 'on') ||
                (isset($_SERVER['HTTP_SSL_FLAG']) && strtoupper($_SERVER['HTTP_SSL_FLAG']) === 'SSL')
            ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
            return header('location:' . str_replace(array(
                'APPID',
                'REDIRECT_URI',
                'SCOPE',
                'STATE',
            ), array(
                $GLOBALS['_W']['account']['key'],
                urlencode($URL),
                self::$scope[(!!$SCOPE) - 0],
                preg_match('/^[a-zA-Z0-9]$/', $STATE) ? $STATE : 0,
            ), self::$OAuth2URL)) || die;
        } else {
            $res = \HttpRequestAPI::ihttp_request(str_replace(array(
                'APPID',
                'APPSECRET',
                'CODE',
            ), array(
                $GLOBALS['_W']['account']['key'],
                $GLOBALS['_W']['account']['secret'],
                $GLOBALS['_GPC']['code'],
            ), self::$getAuthorizationCodeURL));
            $token = json_decode($res['content'], !0);
            if ((!!$SCOPE) - 0) {
                return \HttpRequestAPI::ihttp_request(str_replace(array(
                    'ACCESS_TOKEN',
                    'OPENID',
                ), array(
                    $token['access_token'],
                    $token['openid'],
                ), self::$userInfoURL));
            } else {
                return $res;
            }
        }
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private static $templateMessageURL = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=ACCESS_TOKEN';
    public static function templateMessage($touser, $template_id, $postdata, $url = '', $topcolor = '#FF683F')
    {
        load()->model('account');
        return \HttpRequestAPI::ihttp_request(str_replace('ACCESS_TOKEN', \WeAccount::token(), self::$templateMessageURL), array(
            'touser' => $touser,
            'template_id' => $template_id,
            'url' => trim($url),
            'topcolor' => $topcolor,
            'data' => $postdata,
        ));
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private static $wxappTemplateMessageURL = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=ACCESS_TOKEN';
    public static function wxappTemplateMessage($touser, $template_id, $form_id, $page = '', $value = array(), $color = '#FF683F', $emphasis_keyword = '')
    {
        return \HttpRequestAPI::ihttp_request(str_replace('ACCESS_TOKEN', self::getWxappVailableAccessToken(), self::$wxappTemplateMessageURL), json_encode(array(
            'touser' => $touser,
            'template_id' => $template_id,
            'page' => trim($page),
            'form_id' => $form_id,
            'data' => $value,
            'color' => $color,
            'emphasis_keyword' => trim($emphasis_keyword),
        )));
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private static $addPoiURL = 'https://api.weixin.qq.com/cgi-bin/poi/addpoi?access_token=ACCESS_TOKEN';
    public static function addPoi($JsonBuffer)
    {
        return \HttpRequestAPI::ihttp_request(str_replace('ACCESS_TOKEN', \WeAccount::token(), self::$addPoiURL), $JsonBuffer);
    }
    private static $getPoiURL = 'https://api.weixin.qq.com/cgi-bin/poi/getpoi?access_token=ACCESS_TOKEN';
    public static function getPoi()
    {
        load()->model('account');
        return \HttpRequestAPI::ihttp_request(str_replace('ACCESS_TOKEN', \WeAccount::token(), self::$getPoiURL));
    }
    private static $getPoiListURL = 'https://api.weixin.qq.com/cgi-bin/poi/getpoilist?access_token=ACCESS_TOKEN';
    public static function getPoiList()
    {
        load()->model('account');
        return \HttpRequestAPI::ihttp_request(str_replace('ACCESS_TOKEN', \WeAccount::token(), self::$getPoiListURL));
    }
    private static $updatePoiURL = 'https://api.weixin.qq.com/cgi-bin/poi/updatepoi?access_token=ACCESS_TOKEN';
    public static function updatePoi($JsonBuffer)
    {
        load()->model('account');
        return \HttpRequestAPI::ihttp_request(str_replace('ACCESS_TOKEN', \WeAccount::token(), self::$updatePoiURL), $JsonBuffer);
    }
    private static $delPoiURL = 'https://api.weixin.qq.com/cgi-bin/poi/delpoi?access_token=ACCESS_TOKEN';
    public static function delPoi()
    {
        load()->model('account');
        return \HttpRequestAPI::ihttp_request(str_replace('ACCESS_TOKEN', \WeAccount::token(), self::$delPoiURL));
    }
    private static $getPoiCategoryURL = 'https://api.weixin.qq.com/cgi-bin/poi/getwxcategory?access_token=ACCESS_TOKEN';
    public static function getPoiCategory()
    {
        load()->model('account');
        return \HttpRequestAPI::ihttp_request(str_replace('ACCESS_TOKEN', \WeAccount::token(), self::$getPoiCategoryURL));
    }
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



    public static function getWxappVailableAccessToken() {
        $accounts = pdo_fetchall("SELECT `key`, `secret`, `acid` FROM ".tablename('account_wxapp')." WHERE uniacid = :uniacid ORDER BY `level` DESC ", array(':uniacid' => $GLOBALS['_W']['uniacid']));
        if (empty($accounts)) {
            return error(-1, 'no permission');
        }
        foreach ($accounts as $account) {
            if (empty($account['key']) || empty($account['secret'])) {
                continue;
            }
            $acid = $account['acid'];
            break;
        }
        return self::getAccessToken($account['key'], $account['secret']);
    }

    public static function getAccessToken($key, $secret) {
        $cachekey = "accesstoken:{$key}";
        $cache = cache_load($cachekey);
        if (!empty($cache) && !empty($cache['token']) && $cache['expire'] > TIMESTAMP) {
            return $cache['token'];
        }

        if (empty($key) || empty($secret)) {
            return error('-1', '未填写小程序的 appid 或 appsecret！');
        }

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$key}&secret={$secret}";
        $response = \HttpRequestAPI::ihttp_request($url);
        $response = $response['content'];
        $response = json_decode($response, !0);

        $record = array();
        $record['token'] = $response['access_token'];
        $record['expire'] = TIMESTAMP + $response['expires_in'] - 200;

        cache_write($cachekey, $record);
        return $record['token'];
    }
    private static function makeGetCodeURL() {
        $APPID = $GLOBALS['_W']['account']['key'];
        $APPSECRET = $GLOBALS['_W']['account']['secret'];
    }
}