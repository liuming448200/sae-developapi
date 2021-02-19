<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/general/suggestionModel.php');

class suggestionController extends WebAjaxController {
	
  protected function GetResponse_ () {
    $model = new suggestionModel();
    return $model->GetResponse();
  }
}
