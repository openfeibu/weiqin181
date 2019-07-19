<?PHP

class HttpRequestAPI {

    public static function ihttp_request($url, $post = array(), $headers = array(), $timeout = 5, $allow_curl = true) {
        $urlset = parse_url($url);
        empty($urlset['path']) && ($urlset['path'] = '/');
        empty($urlset['query']) || ($urlset['query'] = "?{$urlset['query']}");
        empty($urlset['port']) && ('http' === $urlset['scheme']) && ($urlset['port'] = '80') || ('https' === $urlset['scheme']) && ($urlset['port'] = '443');
        if($allow_curl && function_exists('curl_init') && function_exists('curl_exec')) {
            $ch = curl_init();
            $port = ('http' === $urlset['scheme'] && '80' === $urlset['port']) || ('https' === $urlset['scheme'] && '443' === $urlset['port']) ? '' : ':' . $urlset['port'];
            curl_setopt($ch, CURLOPT_URL, "{$urlset['scheme']}://{$urlset['host']}{$port}{$urlset['path']}{$urlset['query']}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            if(!empty($post)) {
                curl_setopt($ch, CURLOPT_POST, 1);
                is_array($post) && ($post = http_build_query($post));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            }
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
            if(!empty($headers) && is_array($headers)) foreach($headers as $opt => $value) {
                (false !== strpos($opt, 'CURLOPT_')) && curl_setopt($ch, constant($opt), $value);
                is_numeric($opt) && curl_setopt($ch, $opt, $value);
            }
            $data   = curl_exec($ch);
            $status = curl_getinfo($ch);
            $errno  = curl_errno($ch);
            curl_close($ch);
            if ($errno || empty($data)) return 'error no.:' . $errno . ';' . var_export($status, true);
            return array_merge(self::http_response_parse($data), array(
                'url' => $url,
                'post' => array(
                    'data' => $post,
                    'headers' => $headers,
                )
            ));
        }
        if(/*function_exists('fsock open') || */function_exists('pfsockopen') || function_exists('stream_socket_client')) {
            $method = empty($post) ? 'GET' : 'POST';
            $fdata  = "{$method} {$urlset['path']}{$urlset['query']} HTTP/1.1\r\nHost: {$urlset['host']}\r\n";
            function_exists('gzdecode') && ($fdata .= "Accept-Encoding: gzip, deflate\r\n");
            $fdata .= "Connection: close\r\n";
            if(!empty($headers) && is_array($headers)) foreach($headers as $opt => $value) {
                (strpos($opt, 'CURLOPT_') === false) && ($fdata .= "{$opt}: {$value}\r\n");
            }
            if($post) {
                $body = is_array($post) ? http_build_query($post) : urlencode($post);
                $fdata .= 'Content-Length: ' . strlen($body) . "\r\n\r\n{$body}";
            } else {
                $fdata .= "\r\n";
            }
            //die ('die at using of fsockopen at ' . __FILE__ . ' at line ' . __LINE__);
            $fp = false;
            //(function_exists('fsock open') && ($fp = fsock open($urlset['host'], $urlset['port'], $errno, $errstr, $timeout))) ||
            (function_exists('pfsockopen') && ($fp = pfsockopen($urlset['host'], $urlset['port'], $errno, $errstr, $timeout))) ||
            (function_exists('stream_socket_client') &&
                ($fp = stream_socket_client($urlset['host'] . ':' . $urlset['port'], $errno, $errstr, $timeout)));
            stream_set_blocking($fp, true);
            stream_set_timeout($fp, $timeout);
            if(!$fp) return false;
            fwrite($fp, $fdata);
            $content = '';
            while(!feof($fp)) $content .= fgets($fp, 512);
            fclose($fp);
            return array_merge(self::http_response_parse($content), array(
                'url' => $url,
                'post' => array(
                    'data' => $post,
                    'headers' => $headers,
                )
            ));
        }


        //
        //
        //    if($post) {
        //        if($encodetype == 'URLENCODE') {
        //            $data = http_build_query($post);
        //        } else {
        //            $data = '';
        //            foreach($post as $k => $v) {
        //                $data .= "--$boundary\r\n";
        //                $data .= 'Content-Disposition: form-data; name="'.$k.'"'.(isset($files[$k]) ? '; filename="'.basename($files[$k]).'"; Content-Type: application/octet-stream' : '')."\r\n\r\n";
        //                $data .= $v."\r\n";
        //            }
        //            foreach($files as $k => $file) {
        //                if(!isset($post[$k]) && file_exists($file)) {
        //                    if($fp = @fopen($file, 'r')) {
        //                        $v = fread($fp, filesize($file));
        //                        fclose($fp);
        //                        $data .= "--$boundary\r\n";
        //                        $data .= 'Content-Disposition: form-data; name="'.$k.'"; filename="'.basename($file).'"; Content-Type: application/octet-stream'."\r\n\r\n";
        //                        $data .= $v."\r\n";
        //                    }
        //                }
        //            }
        //            $data .= "--$boundary\r\n";
        //        }
        //        $out = "POST $path HTTP/1.0\r\n";
        //        $header = "Accept: */*\r\n";
        //        $header .= "Accept-Language: zh-cn\r\n";
        //        $header .= $encodetype == 'URLENCODE' ? "Content-Type: application/x-www-form-urlencoded\r\n" : "Content-Type: multipart/form-data; boundary=$boundary\r\n";
        //        $header .= 'Content-Length: '.strlen($data)."\r\n";
        //        $header .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
        //        $header .= "Host: $host:$port\r\n";
        //        $header .= "Connection: Close\r\n";
        //        $header .= "Cache-Control: no-cache\r\n";
        //        $header .= "Cookie: $cookie\r\n\r\n";
        //        $out .= $header;
        //        $out .= $data;
        //    } else {
        //        $out = "GET $path HTTP/1.0\r\n";
        //        $header = "Accept: */*\r\n";
        //        $header .= "Accept-Language: zh-cn\r\n";
        //        $header .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
        //        $header .= "Host: $host:$port\r\n";
        //        $header .= "Connection: Close\r\n";
        //        $header .= "Cookie: $cookie\r\n\r\n";
        //        $out .= $header;
        //    }
        //
        //    $fpflag = 0;
        //    if(!$fp = @fsocketopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout)) {
        //        $context = array(
        //            'http' => array(
        //                'method' => $post ? 'POST' : 'GET',
        //                'header' => $header,
        //                'content' => $post,
        //                'timeout' => $timeout,
        //            ),
        //        );
        //        $context = stream_context_create($context);
        //        $fp = @fopen($scheme.'://'.($ip ? $ip : $host).':'.$port.$path, 'b', false, $context);
        //        $fpflag = 1;
        //    }
        //
        //    if(!$fp) {
        //        return '';
        //    } else {
        //        stream_set_blocking($fp, $block);
        //        stream_set_timeout($fp, $timeout);
        //        @fwrite($fp, $out);
        //        $status = stream_get_meta_data($fp);
        //        if(!$status['timed_out']) {
        //            while (!feof($fp) && !$fpflag) {
        //                $header = @fgets($fp);
        //                $headers .= $header;
        //                if($header && ($header == "\r\n" ||  $header == "\n")) {
        //                    break;
        //                }
        //            }
        //            $GLOBALS['filesockheader'] = $headers;
        //
        //            if($position) {
        //                for($i=0; $i<$position; $i++) {
        //                    $char = fgetc($fp);
        //                    if($char == "\n" && $oldchar != "\r") {
        //                        $i++;
        //                    }
        //                    $oldchar = $char;
        //                }
        //            }
        //
        //            if($limit) {
        //                $return = stream_get_contents($fp, $limit);
        //            } else {
        //                $return = stream_get_contents($fp);
        //            }
        //        }
        //        @fclose($fp);
        //        return $return;
        //    }
        return null;
    }

    public static function http_response_parse($data)
    {
        $rlt = array();
        $split1 = explode("\r\n\r\n", $data, 2);
        $split2 = explode("\r\n", $split1[0], 2);
        preg_match('/^HTTP\/(\S+) (\S+) (.+)?$/', $split2[0], $matches);
        $rlt['code'] = $matches[3];
        $rlt['status'] = $matches[2];
        $rlt['version'] = $matches[1];
        $rlt['responseline'] = $split2[0];
        $header = explode("\r\n", $split2[1]);
        $isgzip = false;
        foreach ($header as $v) {
            $row = explode(': ', $v);
            $key = trim($row[0]);
            $value = trim($row[1]);
            if (isset($rlt['headers'][$key]) && is_array($rlt['headers'][$key])) {
                $rlt['headers'][$key][] = $value;
            } elseif (!empty($rlt['headers'][$key])) {
                $temp = $rlt['headers'][$key];
                unset($rlt['headers'][$key]);
                $rlt['headers'][$key][] = $temp;
                $rlt['headers'][$key][] = $value;
            } else {
                $rlt['headers'][$key] = $value;
            }
            if (!$isgzip && strtolower($key) == 'content-encoding' && strtolower($value) == 'gzip') {
                $isgzip = true;
            }
        }
        if ($isgzip && function_exists('gzdecode')) {
            $rlt['content'] = gzdecode($split1[1]);
        } else {
            $rlt['content'] = $split1[1];
        }
        $rlt['meta'] = $data;
        return $rlt;
    }
}