<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/account/web/RegisterModel.php');

class RegisterController extends WebAjaxController {

  protected function GetResponse_ () {
    $model = new RegisterModel();
    return $model->GetResponse();
  }
}
