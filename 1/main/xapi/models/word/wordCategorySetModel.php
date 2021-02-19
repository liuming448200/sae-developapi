<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');
require(MAIN_ROOT . 'dao/word/category/wordCategoryDao.php');
require(MAIN_ROOT . 'dao/word/wordDao.php');
require(PHP_ROOT . 'libs/util/StringUtility.php');

class wordCategorySetModel extends AjaxModel {

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
    
    if (empty($info['english'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '单词分类英文不能为空';
      return;
    }
    if (empty($info['chinese'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '单词分类中文不能为空';
      return;
    }

    $info['create_time'] = date('Y-m-d H:i:s', time());

    $result = wordCategoryDao::createWordCategoryRow($info);
    if ($result) {
      $result = array(
        'category_id' => $result
      );
      $response->message = '新建单词分类成功';
      $response->data = $result;
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('新建单词分类失败'));
    }
  }

  private function updateAction (&$response) {
    $categoryId = (int)HttpRequestHelper::PostParam('category_id');
    if (empty($categoryId)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '单词分类标识不能为空';
      return;
    }

    $info = HttpRequestHelper::PostParam('info');
    $info = json_decode($info, true);
    if (empty($info)) {
      $response->status = ErrorMsg::REQUEST_PARAM_ERROR;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::REQUEST_PARAM_ERROR];
      return;
    }
    
    if (empty($info['english'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '单词分类英文不能为空';
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::SORRY_MESSAGE, array('单词分类英文不能为空'));
      return;
    }
    if (empty($info['chinese'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '单词分类中文不能为空';
      return;
    }

    $result = wordCategoryDao::updateWordCategoryRow($categoryId, $info);
    if ($result) {
      $response->message = '更新单词分类详情成功';
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('更新单词分类详情失败'));
    }
  }

  private function deleteAction (&$response) {
    $categoryId = (int)HttpRequestHelper::GetParam('category_id');
    if (empty($categoryId)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '单词分类标识不能为空';
      return;
    }

    $result = wordCategoryDao::deleteWordCategoryRow($categoryId);
    if ($result) {
      $result = wordDao::deleteWordsByCategory($categoryId);
      if ($result) {
        $response->message = '删除单词分类成功';
      } else {
        ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('删除单词分类的单词列表失败'));
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('删除单词分类失败'));
    }
  }
}
