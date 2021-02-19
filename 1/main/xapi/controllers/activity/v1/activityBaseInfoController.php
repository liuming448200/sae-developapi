<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/activity/activityBaseInfoModel.php');

class activityBaseInfoController extends WebAjaxController {
	
	protected function GetResponse_ () {
    $model = new activityBaseInfoModel();
    return $model->GetResponse();
  }
}
