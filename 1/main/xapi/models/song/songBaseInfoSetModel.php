<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');
require(MAIN_ROOT . 'dao/song/songDao.php');
require(PHP_ROOT . 'libs/util/StringUtility.php');

class songBaseInfoSetModel extends AjaxModel {

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

  private function createAction (&$response) {
    $language = HttpRequestHelper::GetParam('language');
    if (empty($language)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '儿歌语言不能为空';
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
      $response->message = '儿歌名称不能为空';
      return;
    }
    if (empty($info['content'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '儿歌歌词不能为空';
      return;
    }
    if (empty($info['song'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '儿歌歌曲不能为空';
      return;
    }
    if (empty($info['pic'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '儿歌图片不能为空';
      return;
    }

    $info['create_time'] = date('Y-m-d H:i:s', time());

    $result = songDao::createSongRow($language, $info);
    if ($result) {
      $result = array(
        'song_id' => $result
      );
      $response->message = '新建儿歌成功';
      $response->data = $result;
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('新建儿歌失败'));
    }
  }

  private function updateAction (&$response) {
    $language = HttpRequestHelper::GetParam('language');
    if (empty($language)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '儿歌语言不能为空';
      return;
    }

    $songId = (int)(HttpRequestHelper::PostParam('song_id'));
    if (empty($songId)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '儿歌标识不能为空';
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
      $response->message = '儿歌名称不能为空';
      return;
    }
    if (empty($info['content'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '儿歌歌词不能为空';
      return;
    }
    if (empty($info['song'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '儿歌歌曲不能为空';
      return;
    }
    if (empty($info['pic'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '儿歌图片不能为空';
      return;
    }

    $result = songDao::updateSongRow($language, $songId, $info);
    if ($result) {
      $response->message = '更新儿歌详情成功';
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('更新儿歌详情失败'));
    }
  }

  private function deleteAction (&$response) {
    $language = HttpRequestHelper::GetParam('language');
    if (empty($language)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '儿歌语言不能为空';
      return;
    }

    $songId = (int)(HttpRequestHelper::GetParam('song_id'));
    if (empty($songId)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '儿歌标识不能为空';
      return;
    }

    $result = songDao::deleteSongRow($language, $songId);
    if ($result) {
      $response->message = '删除儿歌成功';
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('删除儿歌失败'));
    }
  }
}
