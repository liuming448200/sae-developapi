<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/general/verifySMSModel.php');

class verifySMSController extends WebAjaxController {

	protected function GetResponse_ () {
		$model = new verifySMSModel();
    return $model->GetResponse();
	}
}
