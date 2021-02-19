<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/account/admin/LoginModel.php');

class LoginController extends WebAjaxController {

  protected function GetResponse_ () {
    $model = new LoginModel();
    return $model->GetResponse();
  }
}
