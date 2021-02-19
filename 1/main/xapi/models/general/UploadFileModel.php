<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');

use sinacloud\sae\Storage as Storage;

class UploadFileModel extends AjaxModel {

  protected $need_login = true;

  public function GetResponse_ () {
    $response = new Response();

    $upload_type = HttpRequestHelper::RequestParam('upload_type');
    if (empty($upload_type)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '文件二级分类参数不能为空';
      return $response;
    }

    $paramname = HttpRequestHelper::PostParam('paramname', 'file'); // 参数

    $limit = HttpRequestHelper::RequestParam('limit');

    $file = @$_FILES[$paramname]['tmp_name'];
    if (!file_exists($file)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '未接收到上传文件';
      return $response;
    }
    $file_content = file_get_contents($file);

    $file_name = @$_FILES[$paramname]['name'];
    $file_type = @$_FILES[$paramname]['type'];

    $temp_arr = explode(".", $file_name);
    $file_ext = array_pop($temp_arr);
    $file_ext = trim($file_ext);
    $file_ext = strtolower($file_ext);

    if ('audio/mp3' == $file_type) {
      $topType = 'audio';
    } elseif ('video/mpeg4' == $file_type) {
      $topType = 'video';
    }

    $new_file_name = $topType . '/' . $upload_type . '/' . date("Ymd") . '/' . time() . '.' . $file_ext;

    $storage = MAIN_STORAGE;

    if (isset($limit)) {
      $storage = PRIVATE_STORAGE;
    }

    $s = new Storage();

    $s->setExceptions(true);

    $ret = $s->getObjectInfo($storage, $new_file_name);
    if (!$ret) {
      $ret = $s->putObject($file_content, $storage, $new_file_name, array(), array('Content-Type' => $file_type));
      if (!$ret) {
        ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('上传文件出错'));
        return $response;
      }

      $url = $s->getUrl($storage, $new_file_name);
      if ($url) {
        $response->data = array(
          'name' => $file_name,
          'url' => $url
        );
        $response->message = '上传文件成功';
      } else {
        ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取上传文件的地址失败'));
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('文件名已经存在，请重新命名文件后上传'));
    }

    return $response;
  }
}
