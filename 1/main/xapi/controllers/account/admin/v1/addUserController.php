<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/account/admin/addUserModel.php');

class addUserController extends WebAjaxController {

	protected function GetResponse_ () {
    $model = new addUserModel();
    return $model->GetResponse();
  }
}
