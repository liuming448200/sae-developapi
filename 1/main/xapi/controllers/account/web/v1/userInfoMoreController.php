<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/account/web/userInfoMoreModel.php');

class userInfoMoreController extends WebAjaxController {

  protected function GetResponse_ () {
    $model = new userInfoMoreModel();
    return $model->GetResponse();
  }
}
