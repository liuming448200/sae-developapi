<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/app/appBaseInfoModel.php');

class appBaseInfoController extends WebAjaxController {
	
	protected function GetResponse_ () {
    $model = new appBaseInfoModel();
    return $model->GetResponse();
  }
}
