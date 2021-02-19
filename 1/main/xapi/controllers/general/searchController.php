<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/general/searchModel.php');

class searchController extends WebAjaxController {
	
	protected function GetResponse_ () {
    $model = new searchModel();
    return $model->GetResponse();
  }
}
