<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');
require(MAIN_ROOT . 'dao/story/storyDao.php');
require(PHP_ROOT . 'libs/util/StringUtility.php');

class storyBaseInfoSetModel extends AjaxModel {

  protected $need_login = true;

  public function GetResponse_ () {
    $response = new Response();

    $uri = $_SERVER['REQUEST_URI'];
    $info = parse_url($uri);
    $action = substr($info['path'], strripos($info['path'], '/') + 1);
    switch ($action) {
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

  /**
   * 添加详情
   * @param type $response
   * @return type id
   */
  private function createAction (&$response) {
    $info = HttpRequestHelper::PostParam('info');
    $info = json_decode($info, true);
    if (empty($info)) {
      $response->status = ErrorMsg::REQUEST_PARAM_ERROR;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::REQUEST_PARAM_ERROR];
      return;
    }
    
    if (empty($info['name'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '故事名称不能为空';
      return;
    }
    if (empty($info['content'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '故事内容不能为空';
      return;
    }
    if (empty($info['implication'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '故事寓意不能为空';
      return;
    }
    if (empty($info['pic'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '故事图片不能为空';
      return;
    }

    $info['create_time'] = date('Y-m-d H:i:s', time());

    $result = storyDao::createStoryRow($info);
    if ($result) {
      $result = array(
        'story_id' => $result
      );
      $response->message = '新建故事成功';
      $response->data = $result;
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('新建故事失败'));
    }
  }
  /**
   * 修改详情
   * @param type $response
   */
  private function updateAction (&$response) {
    $storyId = (int)(HttpRequestHelper::PostParam('story_id'));
    if (empty($storyId)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '故事标识不能为空';
      return;
    }

    $info = HttpRequestHelper::PostParam('info');
    $info = json_decode($info, true);
    if (empty($info)) {
      $response->status = ErrorMsg::REQUEST_PARAM_ERROR;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::REQUEST_PARAM_ERROR];
      return;
    }
    
    if (empty($info['name'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '故事名称不能为空';
      return;
    }
    if (empty($info['content'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '故事内容不能为空';
      return;
    }
    if (empty($info['implication'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '故事寓意不能为空';
      return;
    }
    if (empty($info['pic'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '故事图片不能为空';
      return;
    }

    $result = storyDao::updateStoryRow($storyId, $info);
    if ($result) {
      $response->message = '更新故事详情成功';
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('更新故事详情失败'));
    }
  }
  /**
   * 删除详情
   * @param type $response
   */
  private function deleteAction (&$response) {
    $storyId = (int)(HttpRequestHelper::GetParam('story_id'));
    if (empty($storyId)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '故事标识不能为空';
      return;
    }

    $result = storyDao::deleteStoryRow($storyId);
    if ($result) {
      $response->message = '删除故事成功';
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('删除故事失败'));
    }
  }
}
