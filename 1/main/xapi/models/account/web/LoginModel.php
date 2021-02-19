<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');

class LoginModel extends AjaxModel {

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

    $result = accountDao::login($username, $password);
    if (is_array($result) && $result) {
      $time = time();
      $uid = $result[0]['uid'];
      $mobile = $result[0]['mobile'];
      $username = $result[0]['username'];
      $result = accountDao::checkAccessTokenByUid($uid);
      if (is_array($result)) {
        $login_time = date('Y-m-d H:i:s', $time);
        $accessToken = md5($uid . $time);
        if (count($result) > 0) {
          $ret = accountDao::updateAccessToken($accessToken, $uid, $login_time);
          if ($ret) {
            $extra = accountMoreDao::getUserInfoMoreRow($uid);
            if (is_array($extra)) {
              $child = childAccountDao::getChildInfoRow($uid);
              if (is_array($child)) {
                $response->message = '用户登录成功';
                if (count($extra) > 0 && count($child) > 0) {
                  $response->data = array('uid'=>$uid, 'accessToken'=>$accessToken, 'mobile'=>$mobile, 'username'=>$username, 'extra'=>$extra[0], 'child'=>$child[0]);
                } else if (count($extra) > 0) {
                  $response->data = array('uid'=>$uid, 'accessToken'=>$accessToken, 'mobile'=>$mobile, 'username'=>$username, 'extra'=>$extra[0]);
                } else if (count($child) > 0) {
                  $response->data = array('uid'=>$uid, 'accessToken'=>$accessToken, 'mobile'=>$mobile, 'username'=>$username, 'child'=>$child[0]);
                } else {
                  $response->data = array('uid'=>$uid, 'accessToken'=>$accessToken, 'mobile'=>$mobile, 'username'=>$username);
                }
              } else {
                ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取孩子基本信息失败'));
              }
            } else {
              ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取用户详情失败'));
            }
          } else {
            ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('accessToken更新失败'));
          }
        } else {
          $ret = accountDao::insertAccessToken($accessToken, $uid, $login_time);
          if ($ret) {
            $extra = accountMoreDao::getUserInfoMoreRow($uid);
            if (is_array($extra)) {
              $child = childAccountDao::getChildInfoRow($uid);
              if (is_array($child)) {
                $response->message = '用户登录成功';
                if (count($extra) > 0 && count($child) > 0) {
                  $response->data = array('uid'=>$uid, 'accessToken'=>$accessToken, 'mobile'=>$mobile, 'username'=>$username, 'extra'=>$extra[0], 'child'=>$child[0]);
                } else if (count($extra) > 0) {
                  $response->data = array('uid'=>$uid, 'accessToken'=>$accessToken, 'mobile'=>$mobile, 'username'=>$username, 'extra'=>$extra[0]);
                } else if (count($child) > 0) {
                  $response->data = array('uid'=>$uid, 'accessToken'=>$accessToken, 'mobile'=>$mobile, 'username'=>$username, 'child'=>$child[0]);
                } else {
                  $response->data = array('uid'=>$uid, 'accessToken'=>$accessToken, 'mobile'=>$mobile, 'username'=>$username);
                }
              } else {
                ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取孩子基本信息失败'));
              }
            } else {
              ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取用户详情失败'));
            }
          } else {
            ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('accessToken保存失败'));
          }
        }
      } else {
        ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('用户登录验证失败'));
      }
    } else {
      $response->status = ErrorMsg::USER_LOGIN_FAILED;
      $response->message = sprintf(ErrorMsg::$error_msg_array[ErrorMsg::USER_LOGIN_FAILED], $username);
    }

    return $response;
	}
}
