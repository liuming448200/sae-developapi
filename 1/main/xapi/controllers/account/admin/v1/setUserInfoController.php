<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/account/admin/setUserInfoModel.php');

class setUserInfoController extends WebAjaxController {

	protected function GetResponse_ () {
		$model = new setUserInfoModel();
    return $model->GetResponse();
	}
}
