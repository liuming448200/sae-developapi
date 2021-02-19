<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');
require(MAIN_ROOT . 'dao/course/courseDao.php');
require(PHP_ROOT . 'libs/util/StringUtility.php');

class courseBaseInfoModel extends AjaxModel {
	public function GetResponse_ {
		$response = new Response();

		$uri = $_SERVER['REQUEST_URI'];
		$info = parse_url($uri);
		$action = substr($info['path'], strripos($info['path'], '/') + 1);
		switch ($action) {
			case 'get':
        $this->getAction($response);
        break;
      case 'getlist':
        $this->getListAction($response);
        break;
      default:
        $response->status = ErrorMsg::REQUEST_URL_ERROR;
        $response->message = ErrorMsg::$error_msg_array[ErrorMsg::REQUEST_URL_ERROR];
        break;
		}

		return $response;
	}

	private function getAction (&$response) {
		
	}

	private function getListAction (&$response) {
		
	}
}
