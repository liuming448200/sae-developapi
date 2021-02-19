<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/song/songBaseInfoSetModel.php');

class songBaseInfoSetController extends WebAjaxController {
	
  protected function GetResponse_ () {
    $model = new songBaseInfoSetModel();
    return $model->GetResponse();
  }
}
