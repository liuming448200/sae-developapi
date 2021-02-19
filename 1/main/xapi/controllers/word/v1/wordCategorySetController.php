<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/word/wordCategorySetModel.php');

class wordCategorySetController extends WebAjaxController {
	
  protected function GetResponse_ () {
    $model = new wordCategorySetModel();
    return $model->GetResponse();
  }
}
