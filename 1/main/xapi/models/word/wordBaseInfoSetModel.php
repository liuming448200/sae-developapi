<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');
require(MAIN_ROOT . 'dao/word/wordDao.php');
require(PHP_ROOT . 'libs/util/StringUtility.php');

class wordBaseInfoSetModel extends AjaxModel {

  protected $need_login = true;

  public function GetResponse_ () {
    $response = new Response();

    $uri = $_SERVER['REQUEST_URI'];
    $info = parse_url($uri);
    $action = substr($info['path'], strripos($info['path'], '/') + 1);
    switch ($action) {
      case 'create': //添加详情
        $this->createAction($response);
        break;
      case 'update': //更新详情
        $this->updateAction($response);
        break;
      case 'delete': //删除详情
        $this->deleteAction($response);
        break;
      default:
        $response->status = ErrorMsg::REQUEST_URL_ERROR;
        $response->message = ErrorMsg::$error_msg_array[ErrorMsg::REQUEST_URL_ERROR];
        break;
    }

    return $response;
  }

  private function createAction (&$response) {
    $info = HttpRequestHelper::PostParam('info');
    $info = json_decode($info, true);
    if (empty($info)) {
      $response->status = ErrorMsg::REQUEST_PARAM_ERROR;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::REQUEST_PARAM_ERROR];
      return;
    }
    
    if (empty($info['category_id'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '单词分类标识不能为空';
      return;
    }
    if (empty($info['english'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '单词英文不能为空';
      return;
    }
    if (empty($info['chinese'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '单词中文不能为空';
      return;
    }
    if (empty($info['example_en'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '单词例句不能为空';
      return;
    }
    if (empty($info['example_cn'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '单词例句中文不能为空';
      return;
    }
    if (empty($info['pic'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '单词图片不能为空';
      return;
    }

    $info['create_time'] = date('Y-m-d H:i:s', time());

    $result = wordDao::createWordRow($info);
    if ($result) {
      $result = array(
        'word_id' => $result
      );
      $response->message = '新建单词成功';
      $response->data = $result;
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('新建单词失败'));
    }
  }

  private function updateAction (&$response) {
    $wordId = (int)HttpRequestHelper::PostParam('word_id');
    if (empty($wordId)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '单词标识不能为空';
      return;
    }

    $info = HttpRequestHelper::PostParam('info');
    $info = json_decode($info, true);
    if (empty($info)) {
      $response->status = ErrorMsg::REQUEST_PARAM_ERROR;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::REQUEST_PARAM_ERROR];
      return;
    }
    
    if (empty($info['category_id'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '单词分类标识不能为空';
      return;
    }
    if (empty($info['english'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '单词英文不能为空';
      return;
    }
    if (empty($info['chinese'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '单词中文不能为空';
      return;
    }
    if (empty($info['example_en'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '单词例句不能为空';
      return;
    }
    if (empty($info['example_cn'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '单词例句中文不能为空';
      return;
    }
    if (empty($info['pic'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '单词图片不能为空';
      return;
    }

    $result = wordDao::updateWordRow($wordId, $info);
    if ($result) {
      $response->message = '更新单词详情成功';
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('更新单词详情失败'));
    }
  }

  private function deleteAction (&$response) {
    $wordId = (int)HttpRequestHelper::GetParam('word_id');
    if (empty($wordId)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '单词标识不能为空';
      return;
    }

    $result = wordDao::deleteWordRow($wordId);
    if ($result) {
      $response->message = '删除单词成功';
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('删除单词失败'));
    }
  }
}
