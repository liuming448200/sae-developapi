<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');

class RegisterModel extends AjaxModel {

  public function GetResponse_ () {
    $response = new Response();

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

    $result = accountDao::register($info);
    if ($result) {
      $response->message = '手机号注册成功';
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::USER_MOBILE_FAILED);
    }

    return $response;
  }
}
