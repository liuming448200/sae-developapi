<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');

class userInfoMoreModel extends AjaxModel {

  protected $need_login = true;

  public function GetResponse_ () {
    $response = new Response();

    $uri = $_SERVER['REQUEST_URI'];
    $info = parse_url($uri);
    $action = substr($info['path'], strripos($info['path'], '/') + 1);
    switch ($action) {
      case 'get':
        $this->getAction($response);
        break;
      case 'getlist':
        $this->getListAction($response);
        break;
      case 'create':
        $this->createAction($response);
        break;
      case 'update':
        $this->updateAction($response);
        break;
      case 'delete':
        $this->deleteAction($response);
        break;
      default:
        $response->status = ErrorMsg::REQUEST_URL_ERROR;
        $response->message = ErrorMsg::$error_msg_array[ErrorMsg::REQUEST_URL_ERROR];
        break;
    }

    return $response;
  }

  private function getAction (&$response) {
    $uid = $this->userinfo['uid'];
    if (!$uid) {
      $response->status = ErrorMsg::USER_UID_EMPTY;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_UID_EMPTY];
      return;
    }

    $fields = HttpRequestHelper::GetParam('fieldsList');

    $fields = $fields ? explode(",",$fields) : 1 ;//如果有$fields则分割,没有默认为1
    $fields = $fields && is_array($fields) ? $fields : 1 ; //当空数组或非数字的时候,置为1

    $result = accountMoreDao::getUserInfoMoreRow($uid, $fields);
    if (is_array($result)) {
      if (count($result) > 0) {
        $response->message = '获取用户信息成功';
        $response->data = $result[0];
      } else {
        $response->status = ErrorMsg::SPECIFIC_ERROR;
        $response->message = '获取用户信息为空';
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取用户信息失败'));
    }
  }

  private function getListAction (&$response) {
    $limit = (int)HttpRequestHelper::GetParam('limit');
    $offset = (int)HttpRequestHelper::GetParam('offset');

    $fields = HttpRequestHelper::GetParam('fieldsList');

    $fields = $fields ? explode(",",$fields) : 1 ;//如果有$fields则分割,没有默认为1
    $fields = $fields && is_array($fields) ? $fields : 1 ; //当空数组或非数字的时候,置为1

    $params = array();
    if (!empty($limit)) {
      $params['limit'] = $limit;
    }
    if (!empty($offset)) {
      $params['offset'] = $offset;
    }

    $result = accountMoreDao::getUserInfoMoreList($params, $fields);
    if (is_array($result['list'])) {
      if (count($result['list']) > 0) {
        $response->message = '获取用户信息列表成功';
        $response->data = $result;
      } else {
        $response->status = ErrorMsg::SPECIFIC_ERROR;
        $response->message = '获取用户信息列表为空';
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取用户信息列表失败'));
    }
  }

  private function createAction (&$response) {
    $uid = $this->userinfo['uid'];
    if (!$uid) {
      $response->status = ErrorMsg::USER_UID_EMPTY;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_UID_EMPTY];
      return;
    }

    $info = HttpRequestHelper::PostParam('info');
    $info = json_decode($info, true);
    if (empty($info)) {
      $response->status = ErrorMsg::REQUEST_PARAM_ERROR;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::REQUEST_PARAM_ERROR];
      return;
    }
    
    $info['uid'] = $uid;
    $info['create_time'] = date('Y-m-d H:i:s', time());

    $result = accountMoreDao::createUserInfoMoreRow($info);
    if ($result) {
      $response->message = '新建用户信息成功';
      $response->data = $result;
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('新建用户信息失败'));
    }
  }

  private function updateAction (&$response) {
    $uid = $this->userinfo['uid'];
    if (!$uid) {
      $response->status = ErrorMsg::USER_UID_EMPTY;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_UID_EMPTY];
      return;
    }

    $info = HttpRequestHelper::PostParam('info');
    $info = json_decode($info, true);    
    if (empty($info)) {
      $response->status = ErrorMsg::REQUEST_PARAM_ERROR;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::REQUEST_PARAM_ERROR];
      return;
    }

    $result = accountMoreDao::updateUserInfoMoreRow($uid, $info);
    if ($result) {
      $response->message = '更新用户信息成功';
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('更新用户信息失败'));
    }
  }

  private function deleteAction (&$response) {
    $uid = $this->userinfo['uid'];
    if (!$uid) {
      $response->status = ErrorMsg::USER_UID_EMPTY;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_UID_EMPTY];
      return;
    }

    $result = accountMoreDao::deleteUserInfoMoreRow($uid);
    if ($result) {
      $response->message = '删除用户信息成功';
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('删除用户信息失败'));
    }
  }
}
