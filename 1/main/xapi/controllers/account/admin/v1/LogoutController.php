<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/account/admin/LogoutModel.php');

class LogoutController extends WebAjaxController {

  protected function GetResponse_ () {
    $model = new LogoutModel();
    return $model->GetResponse();
  }
}
