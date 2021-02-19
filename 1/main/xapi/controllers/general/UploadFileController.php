<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/general/UploadFileModel.php');

class UploadFileController extends WebAjaxController {
	
  protected function GetResponse_ () {
    $model = new UploadFileModel();
    return $model->GetResponse();
  }
}
