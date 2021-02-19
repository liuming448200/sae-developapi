<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/content/contentBaseInfoModel.php');

class contentBaseInfoController extends WebAjaxController {
	
	protected function GetResponse_ () {
    $model = new contentBaseInfoModel();
    return $model->GetResponse();
  }
}
