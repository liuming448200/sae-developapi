<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/account/web/childInfoModel.php');

class childInfoController extends WebAjaxController {

  protected function GetResponse_ () {
    $model = new childInfoModel();
    return $model->GetResponse();
  }
}
