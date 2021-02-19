<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/thirdparty/baidu/ttsModel.php');

class ttsController extends WebAjaxController {
	
	protected function GetResponse_ () {
		$model = new ttsModel();
    return $model->GetResponse();
	}
}
