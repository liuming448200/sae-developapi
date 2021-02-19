<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/auth/actionModel.php');

class actionController extends WebAjaxController {
	
	protected function GetResponse_ () {
    $model = new actionModel();
    return $model->GetResponse();
  }
}
