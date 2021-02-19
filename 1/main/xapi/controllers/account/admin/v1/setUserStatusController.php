<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/account/admin/setUserStatusModel.php');

class setUserStatusController extends WebAjaxController {

	protected function GetResponse_ () {
		$model = new setUserStatusModel();
    return $model->GetResponse();
	}
}
