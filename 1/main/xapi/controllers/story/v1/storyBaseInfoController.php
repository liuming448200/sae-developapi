<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/story/storyBaseInfoModel.php');

class storyBaseInfoController extends WebAjaxController {
	
  protected function GetResponse_ () {
    $model = new storyBaseInfoModel();
    return $model->GetResponse();
  }
}
