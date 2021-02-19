<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/word/wordBaseInfoModel.php');

class wordBaseInfoController extends WebAjaxController {
	
	protected function GetResponse_ () {
		$model = new wordBaseInfoModel();
    return $model->GetResponse();
	}
}
