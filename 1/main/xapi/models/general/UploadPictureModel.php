<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');

use sinacloud\sae\Storage as Storage;

class UploadPictureModel extends AjaxModel {

  protected $need_login = true;

  public function GetResponse_ () {
    $response = new Response();

    $upload_type = HttpRequestHelper::RequestParam('upload_type');
    if (empty($upload_type)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '图片二级分类参数不能为空';
      return $response;
    }

    $paramname = HttpRequestHelper::PostParam('paramname', 'image'); // 图片参数

    $limit = HttpRequestHelper::RequestParam('limit');

    $image_file = @$_FILES[$paramname]['tmp_name'];
    if (!file_exists($image_file)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '未接收到上传图片';
      return $response;
    }
    $image_content = file_get_contents($image_file);

    $image_name = @$_FILES[$paramname]['name'];
    $image_type = @$_FILES[$paramname]['type'];

    $temp_arr = explode(".", $image_name);
    $file_ext = array_pop($temp_arr);
    $file_ext = trim($file_ext);
    $file_ext = strtolower($file_ext);

    $new_image_name = 'images' . '/' . $upload_type . '/' . date("Ymd") . '/' . time() . '.' . $file_ext;

    $storage = MAIN_STORAGE;

    if (isset($limit)) {
      $storage = PRIVATE_STORAGE;
    }

    $s = new Storage();

    $s->setExceptions(true);

    $ret = $s->getObjectInfo($storage, $new_image_name);
    if (!$ret) {
      $ret = $s->putObject($image_content, $storage, $new_image_name, array(), array('Content-Type' => $image_type));
      if (!$ret) {
        ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('上传图片出错'));
        return $response;
      }

      $url = $s->getUrl($storage, $new_image_name);
      if ($url) {
        $response->data = array(
          'name' => $image_name,
          'url' => $url
        );
        $response->message = '上传图片成功';
      } else {
        ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取上传图片的地址失败'));
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('文件名已经存在，请重新命名文件后上传'));
    }

    return $response;
  }
}
