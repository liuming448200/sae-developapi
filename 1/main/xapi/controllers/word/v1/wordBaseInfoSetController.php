<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/word/wordBaseInfoSetModel.php');

class wordBaseInfoSetController extends WebAjaxController {
	
  protected function GetResponse_ () {
    $model = new wordBaseInfoSetModel();
    return $model->GetResponse();
  }
}
