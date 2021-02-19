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
      $status = $result[0]['status'];
      if (0 == $status) {
        $time = time();
        $uid = $result[0]['uid'];
        $mobile = $result[0]['mobile'];
        $username = $result[0]['username'];
        $status = $result[0]['status'];
        $result = accountDao::checkAccessTokenByUid($uid);
        if (is_array($result)) {
          $login_time = date('Y-m-d H:i:s', $time);
          $accessToken = md5($uid . $time);
          if (count($result) > 0) {
            $result = accountDao::updateAccessToken($accessToken, $uid, $login_time);
            if ($result) {
              $result = accountDao::getGroupToUserRelation($uid);
              if (is_array($result)) {
                if (count($result) > 0) {
                  $response->message = '用户登录成功';
                  $response->data = array('uid'=>$uid, 'accessToken'=>$accessToken, 'mobile'=>$mobile, 'username'=>$username, 'status'=>$status, 'usergroups'=>$result);
                } else {
                  $response->status = ErrorMsg::SPECIFIC_ERROR;
                  $response->message = '获取用户组映射关系为空';
                }
              } else {
                ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取用户组映射关系失败'));
              }
            } else {
              ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('accessToken更新失败'));
            }
          } else {
            $result = accountDao::insertAccessToken($accessToken, $uid, $login_time);
            if ($result) {
              $result = accountDao::getGroupToUserRelation($uid);
              if (is_array($result)) {
                if (count($result) > 0) {
                  $response->message = '用户登录成功';
                  $response->data = array('uid'=>$uid, 'accessToken'=>$accessToken, 'mobile'=>$mobile, 'username'=>$username, 'status'=>$status, 'usergroups'=>$result);
                } else {
                  $response->status = ErrorMsg::SPECIFIC_ERROR;
                  $response->message = '获取用户组映射关系为空';
                }
              } else {
                ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取用户组映射关系失败'));
              }
            } else {
              ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('accessToken保存失败'));
            }
          }
        } else {
          ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('用户登录验证失败'));
        }
      } else if (1 == $status) {
        $response->status = ErrorMsg::SPECIFIC_ERROR;
        $response->message = '用户已禁用，如需帮助，请联系管理员';
      } else if (2 == $status) {
        $response->status = ErrorMsg::SPECIFIC_ERROR;
        $response->message = '用户已冻结，如需帮助，请联系管理员';
      }
    } else {
      $response->status = ErrorMsg::USER_LOGIN_FAILED;
      $response->message = sprintf(ErrorMsg::$error_msg_array[ErrorMsg::USER_LOGIN_FAILED], $username);
    }

    return $response;
	}
}
