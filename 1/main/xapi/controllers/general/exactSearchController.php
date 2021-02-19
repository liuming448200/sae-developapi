<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/general/exactSearchModel.php');

class exactSearchController extends WebAjaxController {
	
	protected function GetResponse_ () {
    $model = new exactSearchModel();
    return $model->GetResponse();
  }
}
