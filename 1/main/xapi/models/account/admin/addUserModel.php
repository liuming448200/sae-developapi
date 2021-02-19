<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');

class addUserModel extends AjaxModel {

	protected $need_login = true;

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

    $result = accountDao::checkMobileExist($mobile);
    if ($result) {
      $response->status = ErrorMsg::USER_MOBILE_EXIST;
      $response->message = sprintf(ErrorMsg::$error_msg_array[ErrorMsg::USER_MOBILE_EXIST], $mobile);
      return $response;
    }

    $username = trim(HttpRequestHelper::PostParam('username'));
    if (empty($username)) {
      $response->status = ErrorMsg::USER_NAME_EMPTY;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_NAME_EMPTY];
      return $response;
    } else if (!Utility::ValidateIsUserName($username)) {
      $response->status = ErrorMsg::USER_NAME_ERROR;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_NAME_ERROR];
      return $response;
    }

    $result = accountDao::checkUserExist($username);
    if ($result) {
      $response->status = ErrorMsg::USER_EXIST;
      $response->message = sprintf(ErrorMsg::$error_msg_array[ErrorMsg::USER_EXIST], $username);
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

    $usergroups = HttpRequestHelper::PostParam('usergroups');
    if (empty($usergroups)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '所属用户组不能为空';
      return;
    }

    $usergroups = explode(',', $usergroups);

    $time = time();
    $timestamp = date("ymdHis", $time);
    $random = mt_rand(10000, 99999);
    $uid = $timestamp . $random;

    $info = array(
      'uid' => $uid,
      'mobile' => $mobile,
      'username' => $username,
      'password' => md5($password)
    );

    $info['create_time'] = date('Y-m-d H:i:s', $time);

    $result = accountDao::addUser($info);
    if ($result) {
      $result = accountDao::addGroupToUserRelation($uid, $usergroups);
      if ($result) {
        $result = array(
          'uid' => $uid
        );
        $response->message = '添加管理员成功';
        $response->data = $result;
      } else {
        ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('添加用户组映射关系失败'));
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('添加管理员失败'));
    }

    return $response;
	}
}
