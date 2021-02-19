<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/account/web/changePasswordModel.php');

class changePasswordController extends WebAjaxController {

	protected function GetResponse_ () {
    $model = new changePasswordModel();
    return $model->GetResponse();
  }
}
