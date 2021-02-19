<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');

class NotFoundModel extends AjaxModel {
  public function GetResponse_ () {
    $response = new Response();
    $response->status = 404;
    $response->message = '您的请求不存在';
    return $response;
  }
}
