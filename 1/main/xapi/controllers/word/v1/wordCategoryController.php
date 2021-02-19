<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/word/wordCategoryModel.php');

class wordCategoryController extends WebAjaxController {
	
	protected function GetResponse_ () {
		$model = new wordCategoryModel();
    return $model->GetResponse();
	}
}
