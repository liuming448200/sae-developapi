<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/account/admin/mobileCheckModel.php');

class mobileCheckController extends WebAjaxController {

	protected function GetResponse_ () {
    $model = new mobileCheckModel();
    return $model->GetResponse();
  }
}
