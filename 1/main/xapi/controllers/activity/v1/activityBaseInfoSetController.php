<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/activity/activityBaseInfoSetModel.php');

class activityBaseInfoSetController extends WebAjaxController {
	
	protected function GetResponse_ () {
    $model = new activityBaseInfoSetModel();
    return $model->GetResponse();
  }
}
