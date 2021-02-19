<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/account/admin/resetPasswordModel.php');

class resetPasswordController extends WebAjaxController {

	protected function GetResponse_ () {
    $model = new resetPasswordModel();
    return $model->GetResponse();
  }
}
