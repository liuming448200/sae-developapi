<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');

class changePasswordModel extends AjaxModel {

  protected $need_login = true;

	public function GetResponse_ () {
		$response = new Response();

		$uid = $this->userinfo['uid'];

    $oldpassword = trim(HttpRequestHelper::PostParam('oldpassword'));
    if (empty($oldpassword)) {
      $response->status = ErrorMsg::USER_OLD_PASSWORD_EMPTY;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_OLD_PASSWORD_EMPTY];
      return $response;
    } else if (!Utility::ValidatePassword($oldpassword)) {
      $response->status = ErrorMsg::USER_OLD_PASSWORD_ERROR;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_OLD_PASSWORD_ERROR];
      return $response;
    }

    $newpassword = trim(HttpRequestHelper::PostParam('newpassword'));
    if (empty($newpassword)) {
      $response->status = ErrorMsg::USER_NEW_PASSWOED_EMPTY;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_NEW_PASSWOED_EMPTY];
      return $response;
    } else if (!Utility::ValidatePassword($newpassword)) {
      $response->status = ErrorMsg::USER_NEW_PASSWOED_ERROR;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_NEW_PASSWOED_ERROR];
      return $response;
    }

    if ($oldpassword == $newpassword) {
    	$response->status = ErrorMsg::USER_PASSWORD_SAME;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_PASSWORD_SAME];
      return $response;
    }

    $result = accountDao::changePassword($uid, $oldpassword, $newpassword);
    if ($result) {
    	if (1 === $result) {
        $response->status = ErrorMsg::SPECIFIC_ERROR;
        $response->message = '原密码不正确';
    	} else {
    		$response->message = '密码修改成功';
    	}
    } else {
      $response->status = ErrorMsg::ERROR_MESSAGE;
      $response->message = '密码修改失败';
    }

    return $response;
	}
}
