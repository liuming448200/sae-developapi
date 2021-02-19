<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/course/courseBaseInfoModel.php');

class courseBaseInfoController extends WebAjaxController {
	
	protected function GetResponse_ () {
    $model = new courseBaseInfoModel();
    return $model->GetResponse();
  }
}
