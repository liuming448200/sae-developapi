<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/NotFoundModel.php');

class NotFoundController extends WebAjaxController {
	
  protected function GetResponse_ () {
    $model = new NotFoundModel();
    return $model->GetResponse();
  }
}
