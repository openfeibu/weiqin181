<?PHP namespace Slj123Tongcheng\model; defined('IN_IA') || die('Access Denied');

class Util
{
    public function __construct()
    {
    }
    public static function createMySQLUserDefineDistanceFunction()
    {
        SQL::query(" -- MySQL USER `NEED` PRIVILEGES: CREATE ROUTINE, SUPER;
DROP FUNCTION IF EXISTS `GETDISTANCE`;
CREATE FUNCTION `GETDISTANCE` (`MYLAT` DECIMAL(18,10), `MYLNG` DECIMAL(18,10), `THATLAT` DECIMAL(18,10), `THATLNG` DECIMAL(18,10))
  RETURNS DECIMAL(30,15)
  DETERMINISTIC
  LANGUAGE SQL
  CONTAINS SQL
  SQL SECURITY DEFINER /*INVOKER*/
  BEGIN
    DECLARE `SG` DECIMAL(60,30) DEFAULT POW(SIN(((`MYLAT` - `THATLAT`) / 2) * PI() / 180.0), 2);
    DECLARE `SL` DECIMAL(60,30) DEFAULT POW(SIN(((`MYLNG` - `THATLNG`) / 2) * PI() / 180.0), 2);
    DECLARE `SF` DECIMAL(60,30) DEFAULT POW(SIN(((`MYLAT` + `THATLAT`) / 2) * PI() / 180.0), 2);
    DECLARE `S`  DECIMAL(60,30) DEFAULT `SG` * (1 - `SL`) + (1 - `SF`) * `SL`;
    DECLARE `C`  DECIMAL(60,30) DEFAULT (1 - `SG`) * (1 - `SL`) + `SF` * `SL`;
    DECLARE `W`  DECIMAL(60,30) DEFAULT ATAN(SQRT(`S` / `C`));
    DECLARE `R`  DECIMAL(60,30) DEFAULT SQRT(`S` * `C`) / `W`;
    RETURN ((2 * `W` * 6378137.0) * (1 + (1 / 298.257) * (((3 * `R` - 1) / 2 / `C`) * `SF` * (1 - `SG`) - ((3 * `R` + 1) / 2 / `S`) * (1 - `SF`) * `SG`)) / 1000);
  END;
");
    }
    public static function tpl_form_field_file($id, $value = '', $options = array()) {
        $HTML = <<<HTML
<span class="input-group">
    <input type="text" name="{$id}" value="{$value}" class="form-control" placeholder="选择..." readonly />
    <span class="input-group-btn">
    <label class="btn btn-default" for="{$id}">上传附件<input type="file" id="{$id}" class="form-control"
     accept="application/msword" style=" display: none; " onchange="
console.log(this.value);
if(document.querySelector('[id=\'{$id}\']').value === '') {
    alert('请先选择上传文件!');
    return false;
}
//alert(document.getElementById('unload').files[0].type);
var oData = new FormData();
oData.append('uploadWord', document.querySelector('[id=\'{$id}\']').files[0]);
oData.append('op', 'UploadAttachment');
$.ajax({ contentType: false, processData: false, success: function(data, textStatus, jqXHR) {
    $('[name=\'{$id}\']').val(data.file);
}, url: location.href, data: oData, method: 'POST', dataType: 'JSON'});
this.style.display = 'none';
"></label><a class="btn btn-danger ms_mb" onclick="document.querySelector('[name=\'{$id}\']').value = '';">删除附件</a>
    </span>
</span>
HTML;
        return $HTML;
    }
    public static function tpl_ueditor($id, $value = '', $options = array()) {
        $s = '';
        if (!defined('TPL_INIT_UEDITOR')) {
            $s .= '<script type="text/javascript" src="./resource/components/ueditor/ueditor.config.js"></script><script type="text/javascript" src="./resource/components/ueditor/ueditor.all.min.js"></script><script type="text/javascript" src="./resource/components/ueditor/lang/zh-cn/zh-cn.js"></script>';
        }
        $options['readonly'] = (int)(!$options['readonly']);
        $options['height'] = empty($options['height']) ? 200 : $options['height'];
        $s .= !empty($id) ? "<textarea id=\"{$id}\" name=\"{$id}\" type=\"text/plain\" style=\"height:{$options['height']}px;\">{$value}</textarea>" : '';
        $s .= "
	<script type=\"text/javascript\">
	(function() {
			var ueditoroption = {
				'autoClearinitialContent' : false,
				'toolbars' : [['fullscreen', 'source', 'preview', '|', 'bold', 'italic', 'underline', 'strikethrough', 'forecolor', 'backcolor', '|',
					'justifyleft', 'justifycenter', 'justifyright', '|', 'insertorderedlist', 'insertunorderedlist', 'blockquote', 'emotion', 'insertvideo',
					'link', 'removeformat', '|', 'rowspacingtop', 'rowspacingbottom', 'lineheight','indent', 'paragraph', 'fontsize', '|',
					'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol',
					'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', '|', 'anchor', 'map', 'print', 'drafts']],
				'elementPathEnabled' : false,
				'initialFrameHeight': {$options['height']},
				'focus' : false,
				'maximumWords' : 9999999999999,
				readonly: !{$options['readonly']}
			};
			var opts = {
				type :'image',
				direct : false,
				multi : true,
				tabs : {
					'upload' : 'active',
					'browser' : '',
					'crawler' : ''
				},
				path : '',
				dest_dir : '',
				global : false,
				thumb : false,
				width : 0
			};
			UE.registerUI('myinsertimage',function(editor,uiName){
				editor.registerCommand(uiName, {
					execCommand:function(){
						require(['fileUploader'], function(uploader){
							uploader.show(function(imgs){
								if (imgs.length == 0) {
									return;
								} else if (imgs.length == 1) {
									editor.execCommand('insertimage', {
										'src' : imgs[0]['url'],
										'_src' : imgs[0]['attachment'],
										'width' : '100%',
										'alt' : imgs[0].filename
									});
								} else {
									var imglist = [];
									for (i in imgs) {
										imglist.push({
											'src' : imgs[i]['url'],
											'_src' : imgs[i]['attachment'],
											'width' : '100%',
											'alt' : imgs[i].filename
										});
									}
									editor.execCommand('insertimage', imglist);
								}
							}, opts);
						});
					}
				});
				var btn = new UE.ui.Button({
					name: '插入图片',
					title: '插入图片',
					cssRules :'background-position: -726px -77px',
					onclick:function () {
						editor.execCommand(uiName);
					}
				});
				editor.addListener('selectionchange', function () {
					var state = editor.queryCommandState(uiName);
					if (state == -1) {
						btn.setDisabled(true);
						btn.setChecked(false);
					} else {
						btn.setDisabled(false);
						btn.setChecked(state);
					}
				});
				return btn;
			}, 19);
			".(!empty($id) ? "
				$(function(){
					var ue = UE.getEditor('{$id}', ueditoroption);
					$('#{$id}').data('editor', ue);
					$('#{$id}').parents('form').submit(function() {
						if (ue.queryCommandState('source')) {
							ue.execCommand('source');
						}
					});
				});" : '')."
	})();</script>";
        return $s;
    }
    public static function diejson($status = 1, $message = null, $return = null) {
        $ret = array(
            'status' => $status,
        );
        empty($message) || ($ret['message'] = $message);
        empty($return) || ($ret['result'] = $return);

        die(json_encode($ret));
    }
    public static function createNO($table, $field, $prefix) {
        $TABLE = tablename($table);
        do {
            $billno = date('YmdHis') . random(6, true);
        } while(pdo_fetchcolumn("
SELECT COUNT(*)
FROM {$TABLE}
WHERE `{$field}` = :billno
LIMIT 1", array(
            ':billno' => $prefix . $billno,
        )));
        return $prefix . $billno;
    }
    public static function pagination(
        $total,
        $pageIndex,
        $pageSize = 15,
        $url = '',
        $context = array(
            'before'       => 5,
            'after'        => 4,
            'ajaxcallback' => '',
        )
    ) {
        global $_W;
        $pdata = array(
            'tcount'  => 0,
            'tpage'   => 0,
            'cindex'  => 0,
            'findex'  => 0,
            'pindex'  => 0,
            'nindex'  => 0,
            'lindex'  => 0,
            'options' => '',
        );
        if($context['ajaxcallback']) {
            $context['isajax'] = true;
        }

        $pdata['tcount'] = $total;
        $pdata['tpage']  = ceil($total / $pageSize);
        if($pdata['tpage'] <= 1) {
            return '';
        }
        $cindex          = $pageIndex;
        $cindex          = min($cindex, $pdata['tpage']);
        $cindex          = max($cindex, 1);
        $pdata['cindex'] = $cindex;
        $pdata['findex'] = 1;
        $pdata['pindex'] = $cindex > 1 ? $cindex - 1 : 1;
        $pdata['nindex'] = $cindex < $pdata['tpage'] ? $cindex + 1 : $pdata['tpage'];
        $pdata['lindex'] = $pdata['tpage'];

        if($context['isajax']) {
            if(!$url) {
                $url = $_W['script_name'] . '?' . http_build_query($_GET);
            }
            $pdata['faa'] = 'href="javascript:;" page="' . $pdata['findex'] . '" ' .
                ($context['ajaxcallback'] ? 'onclick="' . $context['ajaxcallback'] .
                    '(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['findex'] . '\', this);return false;"' : '');
            $pdata['paa'] = 'href="javascript:;" page="' . $pdata['pindex'] . '" ' .
                ($context['ajaxcallback'] ? 'onclick="' . $context['ajaxcallback'] .
                    '(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['pindex'] . '\', this);return false;"' : '');
            $pdata['naa'] = 'href="javascript:;" page="' . $pdata['nindex'] . '" ' .
                ($context['ajaxcallback'] ? 'onclick="' . $context['ajaxcallback'] .
                    '(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['nindex'] . '\', this);return false;"' : '');
            $pdata['laa'] = 'href="javascript:;" page="' . $pdata['lindex'] . '" ' .
                ($context['ajaxcallback'] ? 'onclick="' . $context['ajaxcallback'] .
                    '(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['lindex'] . '\', this);return false;"' : '');
        } else {
            if($url) {
                $pdata['faa'] = 'href="?' . str_replace('*', $pdata['findex'], $url) . '"';
                $pdata['paa'] = 'href="?' . str_replace('*', $pdata['pindex'], $url) . '"';
                $pdata['naa'] = 'href="?' . str_replace('*', $pdata['nindex'], $url) . '"';
                $pdata['laa'] = 'href="?' . str_replace('*', $pdata['lindex'], $url) . '"';
            } else {
                $_GET['page'] = $pdata['findex'];
                $pdata['faa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
                $_GET['page'] = $pdata['pindex'];
                $pdata['paa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
                $_GET['page'] = $pdata['nindex'];
                $pdata['naa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
                $_GET['page'] = $pdata['lindex'];
                $pdata['laa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
            }
        }

        $html = '<div><ul class="pagination pagination-centered">';
        if($pdata['cindex'] > 1) {
            $html .= "<li><a {$pdata['faa']} class=\"pager-nav\">首页</a></li>";
            $html .= "<li><a {$pdata['paa']} class=\"pager-nav\">&laquo;上一页</a></li>";
        }
        if(!$context['before'] && $context['before'] != 0) {
            $context['before'] = 5;
        }
        if(!$context['after'] && $context['after'] != 0) {
            $context['after'] = 4;
        }

        if($context['after'] != 0 && $context['before'] != 0) {
            $range          = array();
            $range['start'] = max(1, $pdata['cindex'] - $context['before']);
            $range['end']   = min($pdata['tpage'], $pdata['cindex'] + $context['after']);
            if($range['end'] - $range['start'] < $context['before'] + $context['after']) {
                $range['end']   = min($pdata['tpage'], $range['start'] + $context['before'] + $context['after']);
                $range['start'] = max(1, $range['end'] - $context['before'] - $context['after']);
            }
            for($i = $range['start']; $i <= $range['end']; $i++) {
                if($context['isajax']) {
                    $aa = 'href="javascript:;" page="' . $i . '" ' .
                        ($context['ajaxcallback'] ? 'onclick="' . $context['ajaxcallback'] .
                            '(\'' . $_W['script_name'] . $url . '\', \'' . $i . '\', this);return false;"' : '');
                } else {
                    if($url) {
                        $aa = 'href="?' . str_replace('*', $i, $url) . '"';
                    } else {
                        $_GET['page'] = $i;
                        $aa           = 'href="?' . http_build_query($_GET) . '"';
                    }
                }
                $html .= ($i == $pdata['cindex'] ? '<li class="active"><a href="javascript:;">' . $i . '</a></li>' : "<li><a {$aa}>" . $i . '</a></li>');
            }
        }

        if($pdata['cindex'] < $pdata['tpage']) {
            $html .= "<li><a {$pdata['naa']} class=\"pager-nav\">下一页&raquo;</a></li>";
            $html .= "<li><a {$pdata['laa']} class=\"pager-nav\">尾页</a></li>";
        }
        $html .= <<<HTML
</ul><ul class="pagination pagination-centered" style="
    margin-left: 30px;
"><li><span>跳转至</span></li><li><input type="number" id="PageNo" min="1" step="1" style="
    position: relative;
    float: left;
    padding: 6px 12px;
    margin-left: -1px;
    line-height: 1.42857143;
    color: #428bca;
    text-decoration: none;
    background-color: #fff;
    width: 80px;
    border: 1px solid #ddd;
    -webkit-appearance: none;
"></li><li><span>页</span></li></ul><ul class="pagination pagination-centered" style="
    margin-left: 30px;
"><li><span>每页</span></li><li><select id="PageSize" style="
    position: relative;
    float: left;
    padding: 6px 12px;
    margin-left: -1px;
    line-height: 1.42857143;
    color: #428bca;
    text-decoration: none;
    background-color: #fff;
    border: 1px solid #ddd;
    height: 34px;
"><option>10</option><option>20</option><option>30</option><option>40</option><option>50</option><option>100</option><option>200</option><option>300</option><option>500</option><option>1000</option></select></li><li><span>共{$pdata['tpage']}页</span></li><li><span>合计{$total}条</span></li></ul></div>
<script>
(function() {
	(document.getElementById('PageSize').value = "{$GLOBALS['_GPC']['pagesize']}");
	document.getElementById('PageSize').onchange = function() {
		var a = document.createElement('A');
		a.href = window.location.href.match(/(\?|&)pagesize=(\d+)(&|$)/) ?
			window.location.href.replace(/(\?|&)pagesize=(\d+)(&|$)/, '$1pagesize=' + this.value + '$3') :
			window.location.href + '&pagesize=' + this.value;
		a.click();
	};
	document.getElementById('PageNo').onkeypress = function(event) {
		var a = document.querySelector('.pagination.pagination-centered :not(.active) a');
		if(event.keyCode === 13) {
			event.preventDefault();
			a.href = a.href.replace(/(\?|&)page=(\d+)(&|$)/, '$1page=' + this.value + '$3');
			a.click();
		}
	};
})();
</script>
HTML;
        return $html;
    }

    /**
     * 二维数组指定key排序
     * @param        $array
     * @param        $key
     * @param string $order 'asc'||'desc'
     * @return array
     */
    public static function arraySort($array, $key, $order = "asc") {//asc是升序 desc是降序
        $arr_nums = $arr = array();
        foreach($array as $k => $v) {
            $arr_nums[$k] = $v[$key];
        }
        if($order == 'asc') {
            asort($arr_nums);
        } else {
            arsort($arr_nums);
        }
        foreach($arr_nums as $k => $v) {
            $arr[$k] = $array[$k];
        }
        return $arr;
    }

    public static function po($o) {
        echo "<pre>";
        print_r($o);
        echo "</pre>";
    }

    /**
     * 分页
     * @param int $current_page 当前页码
     * @param int $row_count 总行数
     * @param int $page_size 每页行数
     * @param int $show_page 显示页数
     * @return array
     */
    public static function paginationi($current_page, $row_count, $page_size = 20, $show_page = 10) {
        $current_page = intval($current_page);
        $current_page = empty($current_page) ? 1 : $current_page;
        $page_count   = ceil($row_count / $page_size);
        $pagination   = array(
            'pages' => array(),
            'first' => 1,
            'last'  => $page_count,
            'pre'   => ($current_page == 1) ? null : ($current_page - 1),
            'next'  => ($current_page == $page_count) ? null : ($current_page + 1),
        );
        if($page_count <= $show_page) {
            $pagination['pre']  = '';
            $pagination['next'] = '';
        }
        //echo $page_count;
        if($page_count <= $show_page) {
            for($i = 0; $i < $page_count; $i++) {
                $page = array('page' => $i + 1, 'current' => 0);
                if($current_page == ($i + 1))
                    $page['current'] = 1;
                array_push($pagination['pages'], $page);
            }
        } else {
            if($current_page % $show_page == 0) {
                $start = $current_page - $show_page + 1;
            } else
                $start = $current_page - $current_page % $show_page + 1;
            $i = 0;
            while($i < $show_page && ($start + $i) <= $page_count) {
                $page = array('page' => $start + $i, 'current' => 0);
                if($current_page == ($start + $i))
                    $page['current'] = 1;
                array_push($pagination['pages'], $page);
                $i++;
            }
        }
        if($page_count <= 1) {
            $pagination['pages'] = array();
        }
        return $pagination;
    }


    public static function arrayToXml($array) {
        if(!is_array($array) || count($array) <= 0)
            return null;
        $xml = "<xml>";
        $xml .= self::arrayToXml2($array);
        $xml .= "</xml>";
        return $xml;
    }

    private static function arrayToXml2($array) {
        if(!is_array($array) || count($array) <= 0)
            return null;
        $xml = "";
        foreach($array as $key => $val) {
            if(is_array($val)) {
                if(is_numeric($key))
                    $xml .= self::arrayToXml2($val);
                else
                    $xml .= "<" . $key . ">" . self::arrayToXml2($val) . "</" . $key . ">";
            } elseif(is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        return $xml;
    }

    public static function xmlToArray($xml) {
        if(!$xml) {
            return null;
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    public static function arrayToUrlParams($array) {
        $buff = "";
        foreach($array as $k => $v) {
            if($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }

    public static function httpsPost($url, $data, $apiclient_cert, $apiclient_key) {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, false);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //使用证书：cert 与 key 分别属于两个.pem文件
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, $apiclient_cert);
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, $apiclient_key);

        //post提交方式
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            exit("curl出错，错误码:$error");
            //throw new Exception("curl出错，错误码:$error");
            return false;
        }

    }

    public static function getWeekDay($nums) {
        $week_data  = array(
            "周日",
            "周一",
            "周二",
            "周三",
            "周四",
            "周五",
            "周六",
        );
        $nums       = str_split($nums);
        $week_array = array();
        foreach($nums as $num) {
            array_push($week_array, $week_data[$num]);
        }
        return implode("、", $week_array);
    }

    public static function makeSign($data, $apikey) {
        ksort($data);
        $string = self::arrayToUrlParams($data);
        $string = $string . "&key=$apikey";
        $string = md5($string);
        $result = strtoupper($string);
        return $result;
    }
    public static function setGroupConcatMaxLen() {
        $Variables = pdo_fetch("
SHOW SESSION VARIABLES
WHERE `Variable_name` = 'version_compile_machine';");
        $Len = false === strpos($Variables['Value'], '64') ? '4294967295' : '18446744073709551615';
        pdo_query("SET SESSION `group_concat_max_len` = {$Len}; /* 64位版本最大值为：18446744073709551615，32位版本最大值为：4294967295 */");
    }
    public static function hiddenFields() {
        parse_str($_SERVER['QUERY_STRING'], $GET);
        foreach($GET as $KEY => $VALUE) {
            echo "<input type=\"hidden\" name=\"{$KEY}\" value=\"{$VALUE}\">";
        }
    }
    public static function debug($name, $log) {
        $data = array(
            "name"     => $name,
            "log"      => $log,
            'datetime' => date("Y-M-H h:i:s"),
        );
        return pdo_insert("debug_log", $data);
    }
}
