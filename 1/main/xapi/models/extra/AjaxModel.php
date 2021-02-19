<?php
require(PHP_ROOT . 'libs/mvc/ModelBase.php');
require(WEB_ROOT . 'models/extra/ErrorMsg.php');
require(MAIN_ROOT . 'dao/app/appDao.php');
require(PHP_ROOT . 'libs/util/HttpRequestHelper.php');

abstract class AjaxModel extends ModelBase {

  protected $need_login = false;

  protected $is_login = false;
  protected $userinfo = null;

  private $app_key = null;
  private $app_secret = null;

  public function __construct () {
    $this->app_key = HttpRequestHelper::RequestParam('app_key');
    switch ($this->app_key) {
      case WEB_APP_KEY:
        require(MAIN_ROOT . 'dao/account/web/accountDao.php');
        require(MAIN_ROOT . 'dao/account/web/more/accountMoreDao.php');
        require(MAIN_ROOT . 'dao/account/web/child/account/childAccountDao.php');
        break;

      case ADMIN_APP_KEY:
        require(MAIN_ROOT . 'dao/account/admin/accountDao.php');
        break;
      
      default:
        break;
    }
  }

  public function GetResponse () {
    if (!$this->app_key) {
      $response = new Response();
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = 'app_key不能为空';
      return $response;
    } else {
      $result = appDao::getAppRow($this->app_key);
      if (is_array($result) && $result) {
        $this->app_secret = $result[0]['app_secret'];
      } else {
        $response = new Response();
        ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('调用者身份认证失败'));
        return $response;
      }
    }

    $accessToken = HttpRequestHelper::RequestParam('accessToken');

    if ($this->need_login) {
      if ($accessToken) {
        $ret = $this->checkLogin($accessToken);
        if (STATUS_SUCCESS == $ret['status']) {
          $this->is_login = true;
        } else {
          $response = new Response();
          $response->status = $ret['status'];
          $response->message = $ret['message'];
          return $response;
        }
      } else {
        $response = new Response();
        $response->status = ErrorMsg::ACCESS_TOKEN_EMPTY;
        $response->message = ErrorMsg::$error_msg_array[ErrorMsg::ACCESS_TOKEN_EMPTY];
        return $response;
      }
    } else {
      if ($accessToken) {
        $ret = $this->checkLogin($accessToken);
        if (STATUS_SUCCESS == $ret['status']) {
          $this->is_login = true;
        } else {
          $response = new Response();
          $response->status = $ret['status'];
          $response->message = $ret['message'];
          return $response;
        }
      } else {
        $ret = $this->isInitPermission();
        if (STATUS_SUCCESS != $ret['status']) {
          $response = new Response();
          $response->status = $ret['status'];
          $response->message = $ret['message'];
          return $response;
        }
      }
    }

    $response = $this->GetResponse_();
    return $response;
  }

  private function isInitPermission () {
    $ret = array(
      'status' => STATUS_SUCCESS,
      'message' => '调用者身份认证通过',
    );

    //判断时间戳是否合法
    $ts = HttpRequestHelper::RequestParam('ts');
    $now = time();
    if (abs($ts - $now) > REQUEST_TIMERANGE) {
      $ret['status'] = ErrorMsg::ERROR_MESSAGE;
      $ret['message'] = '请求超时';
      return $ret;
    }
    //签名是否正确
    $sign = HttpRequestHelper::RequestParam('sign');
    $method = HttpRequestHelper::RequestParam('method');
    $params = array(
      'app_key='.$this->app_key,
      'app_secret='.$this->app_secret,
      'method='.$method,
      'ts='.$ts);
    sort($params);
    $checkSign = md5(join('&', $params));

    if ($sign != $checkSign) {
      $ret['status'] = ErrorMsg::ERROR_MESSAGE;
      $ret['message'] = '签名不正确';
    }

    return $ret;
  }

  private function checkLogin ($accessToken) {
    $ret = array(
      'status' => STATUS_SUCCESS,
      'message' => '用户登录成功',
    );

    $result = accountDao::checkAccessToken($accessToken);
    if (is_array($result)) {
      if (count($result) > 0) {
        $loginTime = strtotime($result[0]["login_time"]);
        if (($loginTime + USER_LOGIN_TIMEOUT) > time()) {
          $uid = $result[0]['uid'];
          $userinfo = accountDao::getUserInfo($uid);
          $this->userinfo = $userinfo[0];
        } else {
          $ret['status'] = ErrorMsg::ACCESS_TOKEN_TIMEOUT;
          $ret['message'] = ErrorMsg::$error_msg_array[ErrorMsg::ACCESS_TOKEN_TIMEOUT];
        }
      } else {
        $ret['status'] = ErrorMsg::ACCESS_TOKEN_INVALID;
        $ret['message'] = ErrorMsg::$error_msg_array[ErrorMsg::ACCESS_TOKEN_INVALID];
      }
    } else {
      $ret['status'] = ErrorMsg::ACCESS_TOKEN_ERROR;
      $ret['message'] = ErrorMsg::$error_msg_array[ErrorMsg::ACCESS_TOKEN_ERROR];
    }

    return $ret;
  }
}
