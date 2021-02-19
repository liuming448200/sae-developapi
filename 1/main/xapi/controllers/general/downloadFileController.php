<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/general/downloadFileModel.php');

class downloadFileController extends WebAjaxController {
	
	protected function GetResponse_ () {
    $model = new downloadFileModel();
    return $model->GetResponse();
  }
}
