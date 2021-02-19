<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');

class sendSMSModel extends AjaxModel {

    const MEMCACHE_GROUP = 'default';
    
    const CACHE_EXPIRE = 600;

	public function GetResponse_ () {
		$response = new Response();

		$mobile = trim(HttpRequestHelper::PostParam('mobile'));
    	if (empty($mobile)) {
            $response->status = ErrorMsg::USER_MOBILE_EMPTY;
            $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_MOBILE_EMPTY];
            return $response;
        } else if (!Utility::ValidateIsMobile($mobile)) {
            $response->status = ErrorMsg::USER_MOBILE_ERROR;
            $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_MOBILE_ERROR];
            return $response;
        }

        $verifyCode= rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);

        $result = $this->sendSMS($mobile, $verifyCode);
        if ($result) {
        	$response->message = '发送验证码成功';
        } else {
        	ErrorMsg::FillResponseAndLog($response, ErrorMsg::USER_REG_FAILED);
        }

        return $response;
	}

    private function sendSMS ($mobile, $verifyCode) {
        $content = sprintf(TEMPLATE_SMS, $verifyCode);

        $arr = array(
            'mobile' => $mobile,
            'content' => $content
        );
        
        $url = sprintf(MESSAGE_SMS_URL, BECH_SMS_ACCESS_KEY, BECH_SMS_SECRET_KEY);
        $url .= '&' . http_build_query($arr);
        $http = new HttpHandlerCurl();
        $json = $http->get($url);
        $ret = json_decode($json, true);
        if ('01' == $ret['result']) {
            $key = 'sendSMS_' . $mobile;
            $mc = MemcachedClient::GetInstance(self::MEMCACHE_GROUP);
            $mc->set($key, $verifyCode, self::CACHE_EXPIRE);
        } else {
            SaeLog::writelog_debug('send SMS errno:' . $ret['result']);
            return false;
        }

        return true;
    }
}
