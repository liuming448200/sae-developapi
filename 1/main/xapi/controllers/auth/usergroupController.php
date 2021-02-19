<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/auth/usergroupModel.php');

class usergroupController extends WebAjaxController {
	
	protected function GetResponse_ () {
    $model = new usergroupModel();
    return $model->GetResponse();
  }
}
