<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');

class childInfoModel extends AjaxModel {

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

    $cid = (int)HttpRequestHelper::GetParam('cid');
    
    $fields = HttpRequestHelper::GetParam('fieldsList');

    $fields = $fields ? explode(",",$fields) : 1 ;//如果有$fields则分割,没有默认为1
    $fields = $fields && is_array($fields) ? $fields : 1 ; //当空数组或非数字的时候,置为1

    $result = childAccountDao::getChildInfoRow($uid, $fields, $cid);
    if (is_array($result)) {
      if (count($result) > 0) {
        $response->message = '获取孩子基本信息成功';
        $response->data = $result[0];
      } else {
        $response->status = ErrorMsg::SPECIFIC_ERROR;
        $response->message = '获取孩子基本信息为空';
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取孩子基本信息失败'));
    }
  }

  private function getListAction (&$response) {
    $uid = $this->userinfo['uid'];
    if (!$uid) {
      $response->status = ErrorMsg::USER_UID_EMPTY;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_UID_EMPTY];
      return;
    }

    $limit = (int)HttpRequestHelper::GetParam('limit');
    $offset = (int)HttpRequestHelper::GetParam('offset');

    $fields = HttpRequestHelper::GetParam('fieldsList');

    $fields = $fields ? explode(",",$fields) : 1 ;//如果有$fields则分割,没有默认为1
    $fields = $fields && is_array($fields) ? $fields : 1 ; //当空数组或非数字的时候,置为1

    $params = array();
    $params['uid'] = $uid;
    if (!empty($limit)) {
      $params['limit'] = $limit;
    }
    if (!empty($offset)) {
      $params['offset'] = $offset;
    }

    $result = childAccountDao::getChildInfoList($params, $fields);
    if (is_array($result['list'])) {
      if (count($result['list']) > 0) {
        $response->message = '获取孩子信息列表成功';
        $response->data = $result;
      } else {
        $response->status = ErrorMsg::SPECIFIC_ERROR;
        $response->message = '获取孩子信息列表为空';
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取孩子信息列表失败'));
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

    $result = childAccountDao::createChildInfoRow($info);
    if ($result) {
      if (-1 === $result) {
        ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('重置孩子列表默认信息失败'));
      } else {
        $response->message = '新建孩子信息成功';
        $response->data = $result;
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('新建孩子信息失败'));
    }
  }

  private function updateAction (&$response) {
    $uid = $this->userinfo['uid'];
    if (!$uid) {
      $response->status = ErrorMsg::USER_UID_EMPTY;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_UID_EMPTY];
      return;
    }

    $cid = (int)HttpRequestHelper::PostParam('cid');
    if (empty($cid)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '孩子标识不能为空';
      return;
    }

    $info = HttpRequestHelper::PostParam('info');
    $info = json_decode($info, true);
    if (empty($info)) {
      $response->status = ErrorMsg::REQUEST_PARAM_ERROR;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::REQUEST_PARAM_ERROR];
      return;
    }

    $result = childAccountDao::updateChildInfoRow($uid, $cid, $info);
    if ($result) {
      if (-1 === $result) {
        ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('重置孩子列表默认信息失败'));
      } else {
        $response->message = '更新孩子信息成功';
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('更新孩子信息失败'));
    }
  }

  private function deleteAction (&$response) {
    $uid = $this->userinfo['uid'];
    if (!$uid) {
      $response->status = ErrorMsg::USER_UID_EMPTY;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_UID_EMPTY];
      return;
    }

    $cid = (int)HttpRequestHelper::GetParam('cid');
    if (empty($cid)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '孩子标识不能为空';
      return;
    }

    $result = childAccountDao::deleteChildInfoRow($uid, $cid);
    if ($result) {
      $response->message = '删除孩子信息成功';
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('删除孩子信息失败'));
    }
  }
}
