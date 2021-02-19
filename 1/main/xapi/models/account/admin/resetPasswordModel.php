<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');

class resetPasswordModel extends AjaxModel {

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

    $password = trim(HttpRequestHelper::PostParam('password'));
    if (empty($password)) {
      $response->status = ErrorMsg::USER_PASSWD_EMPTY;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_PASSWD_EMPTY];
      return $response;
    } else if (!Utility::ValidatePassword($password)) {
      $response->status = ErrorMsg::USER_PASSWORD_ERROR;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_PASSWORD_ERROR];
      return $response;
    }

    $result = accountDao::resetPassword($mobile, $password);
    if ($result) {
    	if (1 === $result) {
        $response->status = ErrorMsg::USER_PASSWORD_SAME;
        $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_PASSWORD_SAME];
    	} else {
    		$response->message = '重置密码成功';
    	}
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('重置密码失败'));
    }

    return $response;
	}
}
