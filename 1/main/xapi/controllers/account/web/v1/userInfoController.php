<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/account/web/userInfoModel.php');

class userInfoController extends WebAjaxController {

	protected function GetResponse_ () {
		$model = new userInfoModel();
    return $model->GetResponse();
	}
}
