<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');

class mobileCheckModel extends AjaxModel {

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

		$result = accountDao::checkMobileExist($mobile);
		if ($result) {
			$response->status = ErrorMsg::USER_MOBILE_EXIST;
    	$response->message = sprintf(ErrorMsg::$error_msg_array[ErrorMsg::USER_MOBILE_EXIST], $mobile);
		} else {
			$response->message = '手机号可以注册';
		}

		return $response;
	}
}
