<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/general/sendImageVerifyCodeModel.php');

class sendImageVerifyCodeController extends WebAjaxController {

	protected function GetResponse_ () {
		$model = new sendImageVerifyCodeModel();
    return $model->GetResponse();
	}
}
