<?php

/**
 * 模拟HTTP请求类，通过 CURL 发起请求
 */
class HttpHandlerCurl {

  const CHARSET_UTF8 = 'UTF-8';
  const CHARSET_GBK = 'GBK';
  const DEFAULT_TIMEOUT = 30;

  private $responseCharset;
  private $ch;

  /**
   * 构造函数
   * @param type $responseCharset  响应字符集。非UTF-8的站重写此变量
   * @param int $timeout 超时时间
   */
  public function __construct($responseCharset = self::CHARSET_UTF8, $timeout = self::DEFAULT_TIMEOUT) {
    $this->responseCharset = $responseCharset;

    $this->ch = curl_init();

    $opt = array(
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_FOLLOWLOCATION => false,
      CURLOPT_COOKIEJAR => tempnam('saemc://tmp/', 'HttpHandlerCurl_Cookie_'),
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_TIMEOUT => $timeout,
      CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
    );

    curl_setopt_array($this->ch, $opt);
  }

  public function __destruct() {
    curl_close($this->ch);
  }

  /**
   * 任意编码转为utf8，接收response后调用
   *
   * @param mixed $anyData
   *
   * @return mixed
   */
  private function iconvAny2utf8($anyData) {
    $ret = $anyData;

    if ($this->responseCharset != self::CHARSET_UTF8) {
      $ret = self::iconvAny($this->responseCharset, self::CHARSET_UTF8, $anyData);
    }

    return $ret;
  }

  /**
   * utf8转为任意编码，向外发request时调用
   *
   * @param mixed $utf8Data
   *
   * @return mixed
   */
  private function iconvUtf82Any($utf8Data) {
    $ret = $utf8Data;

    if ($this->responseCharset != self::CHARSET_UTF8) {
      $ret = self::iconvAny(self::CHARSET_UTF8, $this->responseCharset, $utf8Data);
    }

    return $ret;
  }

  /**
   * 字符集转换，支持字符串和数组类型
   *
   * @param string $in_charset
   * @param string $out_charset
   * @param mixed $data
   * @return mixed
   */
  private static function iconvAny($in_charset, $out_charset, $data) {
    $ret;

    if (is_array($data)) {
      //数组，递归处理

      $ret = array();

      foreach ($data as $k => $v) {
        $ret[self::iconvAny($in_charset, $out_charset, $k)] = self::iconvAny($in_charset, $out_charset, $v);
      }
    } else {
      // 字符串，iconv转码
      $ret = iconv($in_charset, $out_charset . "//IGNORE", $data);

      // 字符串iconv转码失败，尝试用 mb_convert_encoding 转码
      if ($data !== false && $ret === false) {
        $ret = mb_convert_encoding($data, $out_charset, $in_charset);
      }
    }

    return $ret;
  }

  /**
   * HTTP GET
   *
   * @todo $headers 未处理
   *
   * @param string $url
   * @param array $headers
   * @param array $opts array[cookies, header]
   * @return string
   */
  public function get($url, array $headers = array(), array $opts = array()) {
    $opt = array(
      CURLOPT_URL => $this->iconvUtf82Any($url),
      CURLOPT_POST => 0,
    );

    if (isset($opts['cookies']) && $opts['cookies']) {
      $opt[CURLOPT_COOKIE] = $opts['cookies'];
    }

    if (isset($opts['header']) && $opts['header']) {
      $opt[CURLOPT_HEADER] = true;
    }

    curl_setopt_array($this->ch, $opt);
    $data = curl_exec($this->ch);

    $data = $this->iconvAny2utf8($data);

    return $data;
  }
	/**
   * HTTP POSTJson
   *
   * @todo $headers 未处理
   *
   * @param string $url
   * @param array $query
   * @param array $headers
   * @param array $opts array[cookies, header]
   * @return string
   */
  public function postJson($url, array $query, array $headers = array(), array $opts = array()) {
    $opt = array(
      CURLOPT_URL => $this->iconvUtf82Any($url),
      CURLOPT_POST => 1,
      CURLOPT_POSTFIELDS => json_encode($query,true),
			CURLOPT_HTTPHEADER=>array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen(json_encode($query,true))),

		);

    if (isset($opts['cookies']) && $opts['cookies']) {
      $opt[CURLOPT_COOKIE] = $opts['cookies'];
    }

    if (isset($opts['header']) && $opts['header']) {
			$opt[CURLOPT_HEADER] = true;
		}

    curl_setopt_array($this->ch, $opt);
    $data = curl_exec($this->ch);

    $data = $this->iconvAny2utf8($data);

    return $data;
  }

  /**
   * HTTP POST
   *
   * @todo $headers 未处理
   *
   * @param string $url
   * @param array $query
   * @param array $headers
   * @param array $opts array[cookies, header]
   * @return string
   */
  public function post($url, array $query, array $headers = array(), array $opts = array()) {
    $opt = array(
      CURLOPT_URL => $this->iconvUtf82Any($url),
      CURLOPT_POST => 1,
      CURLOPT_POSTFIELDS => http_build_query((array)$query),
    );

    if (isset($opts['cookies']) && $opts['cookies']) {
      $opt[CURLOPT_COOKIE] = $opts['cookies'];
    }

    if (isset($opts['header']) && $opts['header']) {
      $opt[CURLOPT_HEADER] = true;
    }

    curl_setopt_array($this->ch, $opt);
    $data = curl_exec($this->ch);

    $data = $this->iconvAny2utf8($data);

    return $data;
  }

  /**
   *
   * @param : $requests
   *        	= array(
   *        	array('url' => 'http://1', 'timeout' => '', 'host' => ''),
   *        	array('url' => 'http://1', 'timeout' => '', 'host' => '')
   *        	);
   * @param  $time_ms   true--s, false--ms
   * @return : $results = array(
   *         'content1',
   *         'content1',
   *         );
   */
  public function multiRequest($requests,$time_ms=false)
  {

  	$mh = curl_multi_init();
  	$handles = array();
  	$results = array();
  	$map = array();
  	foreach ($requests as $key => $value) {
  		if ($value['url']) {
  			$handles[$key] = curl_init($value['url']);
  			if ($value['timeout']) {
  				if(true === $time_ms){
  					curl_setopt($handles[$key], CURLOPT_NOSIGNAL, 1);
  					if (empty($value['timeout'])) {
  						$requests[$key]['timeout'] = $value['timeout'] = 1000;
  					}
  					curl_setopt($handles[$key], CURLOPT_TIMEOUT_MS, $value['timeout']);
  					curl_setopt($handles[$key], CURLOPT_CONNECTTIMEOUT_MS, 100);  //尝试连接等待的时间
  					//             			curl_setopt($handles[$key], CURLE_OPERATION_TIMEOUTED, 1);  //尝试连接等待的时间
  				}
  				else{
  					if (empty($value['timeout'])) {
  						$requests[$key]['timeout'] = $value['timeout'] = 1;
  					}
  					curl_setopt($handles[$key], CURLOPT_TIMEOUT, $value['timeout']);
  					curl_setopt($handles[$key], CURLOPT_CONNECTTIMEOUT, 1);  //尝试连接等待的时间

  				}
  			}
  			else{
  				curl_setopt($handles[$key], CURLOPT_TIMEOUT, 1);
  				curl_setopt($handles[$key], CURLOPT_CONNECTTIMEOUT, 1);  //尝试连接等待的时间
  			}

  			curl_setopt($handles[$key], CURLOPT_FAILONERROR, 1);
  			curl_setopt($handles[$key], CURLOPT_RETURNTRANSFER, true);
  			if ($value['data']) {
  				curl_setopt($handles[$key], CURLOPT_POST, 1);
  				curl_setopt($handles[$key], CURLOPT_POSTFIELDS, $value['data']);
  			}
  			curl_multi_add_handle($mh, $handles[$key]);
  			$map[$handles[$key]] = array('key'=>$key,'value'=>$value);
  		} else {
  			unset($requests[$key]);
  		}
  	}

  	if (empty($handles)) {
  		curl_multi_close($mh);
  		return $results;
  	}

  	$responses = array();
  	do {
  		while (($code = curl_multi_exec($mh, $active)) == CURLM_CALL_MULTI_PERFORM) ;

  		if ($code != CURLM_OK) {
  			break;
  		}
  		$done = '';
  		while ($done = curl_multi_info_read($mh)) {
  			$info = curl_getinfo($done['handle']);
  			$error = curl_error($done['handle']);
  			$errno = curl_errno($done['handle']);
  			$results[$map[$done['handle']]['key']] = curl_multi_getcontent($done['handle']);
  			// remove the curl handle that just completed
  			curl_multi_remove_handle($mh, $done['handle']);
  			curl_close($done['handle']);
  		}

  		// Block for data in / output; error handling is done by curl_multi_exec
  		if ($active > 0) {
  			curl_multi_select($mh, 0.5);
  		}

  	} while ($active);

  	curl_multi_close($mh);
  	return $results;
  }
  /**
   * HTTP PUT
   *
   * @todo $headers 未处理
   *
   * @param string $url
   * @param array $query
   * @param array $headers
   * @param array $opts array[cookies, header]
   * @return string
   */
  public function put($url, array $query, array $headers = array(), array $opts = array()) {
  	$opt = array(
  			CURLOPT_URL => $this->iconvUtf82Any($url),
  			CURLOPT_POST => 0,
  			CURLOPT_CUSTOMREQUEST => 'PUT',
  			CURLOPT_POSTFIELDS => http_build_query((array)$query),
  	);

  	if (isset($opts['cookies']) && $opts['cookies']) {
  		$opt[CURLOPT_COOKIE] = $opts['cookies'];
  	}

  	if (isset($opts['header']) && $opts['header']) {
  		$opt[CURLOPT_HEADER] = true;
  	}

  	curl_setopt_array($this->ch, $opt);
  	$data = curl_exec($this->ch);

  	$data = $this->iconvAny2utf8($data);
  	return $data;
  }

  public function putJson($url, array $query, array $headers = array(), array $opts = array()){
    $opt = array(
        CURLOPT_URL => $this->iconvUtf82Any($url),
        CURLOPT_POST => 0,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS => json_encode($query),
        CURLOPT_HTTPHEADER=>array(
            'Content-Type:application/json;charset=utf-8',
            'Accept:application/json',
        ),
    );

  	if (isset($opts['cookies']) && $opts['cookies']) {
  		$opt[CURLOPT_COOKIE] = $opts['cookies'];
  	}

  	if (isset($opts['header']) && $opts['header']) {
  		$opt[CURLOPT_HEADER] = true;
  	}

  	curl_setopt_array($this->ch, $opt);
  	$data = curl_exec($this->ch);

  	$data = $this->iconvAny2utf8($data);
  	return $data;
  }

  /**
   * HTTP DELETE
   *
   * @param string $url
   * @param array/string $query
   * @param array $headers
   * @param array $opts array[cookies, header]
   * @return string
   */
  public function delete($url, $query = '', array $headers = array(), array $opts = array()) {
    $query = (is_array($query)) ? http_build_query($query) : $query; // 保证string

  	$opt = array(
    	CURLOPT_URL => $this->iconvUtf82Any($url),
    	CURLOPT_FOLLOWLOCATION => 1,
    	CURLOPT_CUSTOMREQUEST => 'DELETE',
    	CURLOPT_POSTFIELDS => $query, // string
  	);

  	if (isset($opts['cookies']) && $opts['cookies']) {
  		$opt[CURLOPT_COOKIE] = $opts['cookies'];
  	}

  	curl_setopt_array($this->ch, $opt);
  	$data = curl_exec($this->ch);

  	$data = $this->iconvAny2utf8($data);
  	return $data;
  }

  /**
   * HTTP POST 上传文件binary流
   *
   * @todo $headers 未处理
   * @author xielei14
   *
   * @param string $url
   * @param array $query
   * @param array $headers
   * @param array $opts array[cookies, header]
   * @return string
   */
  public function postFile($url, $data, array $headers = array(), array $opts = array()) {
    $opt = array(
      CURLOPT_URL => $this->iconvUtf82Any($url),
      CURLOPT_POST => 1,
      CURLOPT_POSTFIELDS => $data,
    );

    if (isset($opts['cookies']) && $opts['cookies']) {
      $opt[CURLOPT_COOKIE] = $opts['cookies'];
    }

    if (isset($opts['header']) && $opts['header']) {
      $opt[CURLOPT_HEADER] = true;
    }

    curl_setopt_array($this->ch, $opt);
    $data = curl_exec($this->ch);

    $data = $this->iconvAny2utf8($data);

    return $data;
  }

}
