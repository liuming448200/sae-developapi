<?php
require(WEB_ROOT . 'controllers/extra/WebAjaxController.php');
require(WEB_ROOT . 'models/general/UploadPictureModel.php');

class UploadPictureController extends WebAjaxController {
	
  protected function GetResponse_ () {
    $model = new UploadPictureModel();
    return $model->GetResponse();
  }
}
