<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');

class verifySMSModel extends AjaxModel {

  const MEMCACHE_GROUP = 'default';

	public function GetResponse_ () {
		$response = new Response();

		$mobile = trim(HttpRequestHelper::GetParam('mobile'));
		if (empty($mobile)) {
      $response->status = ErrorMsg::USER_MOBILE_EMPTY;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_MOBILE_EMPTY];
      return $response;
    } else if (!Utility::ValidateIsMobile($mobile)) {
      $response->status = ErrorMsg::USER_MOBILE_ERROR;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_MOBILE_ERROR];
      return $response;
    }

    $verifyCode = trim(HttpRequestHelper::GetParam('verifyCode'));
    if (empty($verifyCode)) {
      $response->status = ErrorMsg::USER_AUTHCODE_EMPTY;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_AUTHCODE_EMPTY];
      return $response;
    }

    $result = $this->verifySMS($mobile, $verifyCode);
    if ($result) {
    	$response->message = '短信验证码验证通过';
    } else {
    	ErrorMsg::FillResponseAndLog($response, ErrorMsg::USER_NAME_MOBILE_AUTHCODE_ERROR);
    }

    return $response;
	}

  private function verifySMS ($mobile, $verifyCode) {
    $key = 'sendSMS_' . $mobile;
    $mc = MemcachedClient::GetInstance(self::MEMCACHE_GROUP);
    $result = $mc->get($key);
    if ($result) {
      if ($result != $verifyCode) {
        SaeLog::writelog_debug('验证码不正确' . $verifyCode);
        return false;
      }
    } else {
      SaeLog::writelog_debug('验证码过期' . $verifyCode);
      return false;
    }

    $mc->delete($key);
    return true;
  }
}
