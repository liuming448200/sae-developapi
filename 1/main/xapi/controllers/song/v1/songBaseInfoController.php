<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/song/songBaseInfoModel.php');

class songBaseInfoController extends WebAjaxController {
	
	protected function GetResponse_ () {
		$model = new songBaseInfoModel();
		return $model->GetResponse();
	}
}
