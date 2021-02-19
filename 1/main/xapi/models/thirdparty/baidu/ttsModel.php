<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');
require(MAIN_ROOT . 'dao/thirdparty/baidu/text2audio.php');

class ttsModel extends AjaxModel {

	const MEMCACHE_GROUP = 'default';

	const CACHE_EXPIRE = 86400;

	public function GetResponse_ () {
		$response = new Response();
		
		$text = HttpRequestHelper::GetParam('text');
		if (empty($text)) {
			$response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '文本不能为空';
			return $response;
		} else if (strlen($text) >= 1024) {
			$response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '文本长度必须小于1024字节';
			return $response;
		}

		$cuid = HttpRequestHelper::GetParam('_realip');

		$result = text2audio::checkAccessToken();
		if (is_array($result) && $result) {
			$createTime = strtotime($result[0]['create_time']);
			$expireTime = $result[0]['expires_in'];
			if (($createTime + $expireTime) > time()) {
				$access_token = $result[0]['access_token'];
				$this->getAudio($text, $access_token, $cuid);
			} else {
				$refresh_token = $result[0]['refresh_token'];
				$result = $this->refreshAccessToken($refresh_token);
				if (isset($result['error'])) {
					ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, 
						array('error' => $result['error'], 'error_description' => $result['error_description']));
					return $response;
				} else {
					$result['create_time'] = date('Y-m-d H:i:s', time());
					$ret = text2audio::updateAccessToken($refresh_token, $result);
					if ($ret) {
						$access_token = $result['access_token'];
						$this->getAudio($text, $access_token, $cuid);
					} else {
						ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('access_token更新失败'));
						return $response;
					}
				}
			}
		} else {
			$result = $this->getAccessToken();
			if (isset($result['error'])) {
				ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, 
					array('error' => $result['error'], 'error_description' => $result['error_description']));
				return $response;
			} else {
				$result['create_time'] = date('Y-m-d H:i:s', time());
				$ret = text2audio::insertAccessToken($result);
				if ($ret > 0) {
					$access_token = $result['access_token'];
					$this->getAudio($text, $access_token, $cuid);
				} else {
					ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('access_token保存失败'));
					return $response;
				}
			}
		}
	}

	private function getAccessToken () {
		$url = BAIDU_OAUTH_URL;

		$params = array(
			'grant_type' => BAIDU_GRANT_TYPE,
			'client_id' => BAIDU_CLIENT_ID,
			'client_secret' => BAIDU_CLIENT_SECRET
		);

		$http = new HttpHandlerCurl();
		$json = $http->post($url, $params);
		$ret = json_decode($json, true);
		return $ret;
	}

	private function refreshAccessToken ($refresh_token) {
		$url = BAIDU_OAUTH_URL;

		$params = array(
			'grant_type' => BAIDU_GRANT_TYPE,
			'refresh_token' => $refresh_token,
			'client_id' => BAIDU_CLIENT_ID,
			'client_secret' => BAIDU_CLIENT_SECRET
		);

		$http = new HttpHandlerCurl();
		$json = $http->post($url, $params);
		$ret = json_decode($json, true);
		return $ret;
	}

	private function getAudio ($text, $access_token, $cuid) {
		$key = 'baidu_tts_api_text2audio_' . $text;
  	$mc = MemcachedClient::GetInstance(self::MEMCACHE_GROUP);
  	$result = $mc->get($key);
    if ($result) {
      header("Content-Type: audio/mp3");
      header("Content-Length: " . strlen($result));
			echo $result;
			exit;
    }
    
		$url = BAIDU_TTS_URL;

		$params = array(
			'tex' => $text,
			'lan' => 'zh',
			'tok' => $access_token,
			'ctp' => 1,
			'cuid' => $cuid
		);

		$url .= '?' . http_build_query($params);
		$http = new HttpHandlerCurl();
		$result = $http->get($url, array(), array('header' => true));

		list($response_headers, $result) = explode("\r\n\r\n", $result, 2);

		$arr = explode("\r\n", $response_headers);
		$contentTypeStr = $arr[3];
		$contentLengthStr = $arr[4];
		$contentType = substr($contentTypeStr, strpos($contentTypeStr, ':') + 2);
		$contentLength = substr($contentLengthStr, strpos($contentLengthStr, ':') + 2);

		if ('audio/mp3' == $contentType) {
			$mc->set($key, $result, self::CACHE_EXPIRE);
		
			header("Content-type: " . $contentType);
			header("Content-Length: " . $contentLength);
			echo $result;
			exit;
		} else if ('application/json' == $contentType) {
			$result = json_decode($result, true);

			ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, 
					array('err_no' => $result['err_no'], 'err_msg' => $result['err_msg'], 'sn' => $result['sn'], 'idx' => $result['idx']));
			return $response;
		}
	}
}
