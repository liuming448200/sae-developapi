<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/story/storyBaseInfoSetModel.php');

class storyBaseInfoSetController extends WebAjaxController {
	
  protected function GetResponse_ () {
    $model = new storyBaseInfoSetModel();
    return $model->GetResponse();
  }
}
