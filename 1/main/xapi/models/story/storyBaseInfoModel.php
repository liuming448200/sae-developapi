<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');
require(MAIN_ROOT . 'dao/story/storyDao.php');
require(PHP_ROOT . 'libs/util/StringUtility.php');

class storyBaseInfoModel extends AjaxModel {
  public function GetResponse_ () {
    $response = new Response();

    //解析请求
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
      default:
        $response->status = ErrorMsg::REQUEST_URL_ERROR;
        $response->message = ErrorMsg::$error_msg_array[ErrorMsg::REQUEST_URL_ERROR];
        break;
    }
    
    return $response;
  }
  /**
   * 获取详情
   * @param type $response
   */
  private function getAction (&$response) {
    $storyId = (int)HttpRequestHelper::GetParam('story_id');
    if (empty($storyId)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '故事标识不能为空';
      return;
    }
    
    $fields = HttpRequestHelper::GetParam('fieldsList');

    $fields = $fields ? explode(",",$fields) : 1 ;//如果有$fields则分割,没有默认为1
    $fields = $fields && is_array($fields) ? $fields : 1 ; //当空数组或非数字的时候,置为1

    $result = storyDao::getStoryRow($storyId, $fields);
    if (is_array($result)) {
      if (count($result) > 0) {
        $response->message = '获取故事详情成功';
        $response->data = $result[0];
      } else {
        $response->status = ErrorMsg::SPECIFIC_ERROR;
        $response->message = '获取故事详情为空';
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取故事详情失败'));
    }
  }

  /**
   * 获取列表
   * @param type $response
   */
  private function getListAction (&$response) {
    $limit = (int)HttpRequestHelper::GetParam('limit');
    $offset = (int)HttpRequestHelper::GetParam('offset');

    $fields = HttpRequestHelper::GetParam('fieldsList');

    $fields = $fields ? explode(",",$fields) : 1 ;//如果有$fields则分割,没有默认为1
    $fields = $fields && is_array($fields) ? $fields : 1 ; //当空数组或非数字的时候,置为1

    //搜索条件
    $params = array();
    if (!empty($limit)) {
      $params['limit'] = $limit;
    }
    if (!empty($offset)) {
      $params['offset'] = $offset;
    }

    $result = storyDao::getStoryList($params, $fields);
    if (is_array($result['list'])) {
      if (count($result['list']) > 0) {
        $response->message = '获取故事列表成功';
        $response->data = $result;
      } else {
        $response->status = ErrorMsg::SPECIFIC_ERROR;
        $response->message = '获取故事列表为空';
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取故事列表失败'));
    }
  }
}
