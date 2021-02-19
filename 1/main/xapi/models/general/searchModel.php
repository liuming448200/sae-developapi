<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');
require(MAIN_ROOT . 'dao/general/search/searchDao.php');

class searchModel extends AjaxModel {

	public function GetResponse_ () {
		$response = new Response();

		return $response;
	}
}
