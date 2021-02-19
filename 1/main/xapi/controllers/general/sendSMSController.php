<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/general/sendSMSModel.php');

class sendSMSController extends WebAjaxController {

	protected function GetResponse_ () {
		$model = new sendSMSModel();
    return $model->GetResponse();
	}
}
