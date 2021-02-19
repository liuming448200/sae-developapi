<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/auth/menuModel.php');

class menuController extends WebAjaxController {
	
	protected function GetResponse_ () {
    $model = new menuModel();
    return $model->GetResponse();
  }
}
