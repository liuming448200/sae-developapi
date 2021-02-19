<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/general/relatedSearchModel.php');

class relatedSearchController extends WebAjaxController {
	
	protected function GetResponse_ () {
    $model = new relatedSearchModel();
    return $model->GetResponse();
  }
}
