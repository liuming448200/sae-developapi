<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/general/checkImageVerifyCodeModel.php');

class checkImageVerifyCodeController extends WebAjaxController {

	protected function GetResponse_ () {
		$model = new checkImageVerifyCodeModel();
    return $model->GetResponse();
	}
}
