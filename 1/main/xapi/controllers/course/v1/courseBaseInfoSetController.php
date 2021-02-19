<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/course/courseBaseInfoSetModel.php');

class courseBaseInfoSetController extends WebAjaxController {
	
	protected function GetResponse_ () {
    $model = new courseBaseInfoSetModel();
    return $model->GetResponse();
  }
}
