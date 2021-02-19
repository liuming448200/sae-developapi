<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');

class appBaseInfoModel extends AjaxModel {

	protected $need_login = true;

	public function GetResponse_ () {
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
		$app_key = (int)HttpRequestHelper::GetParam('app_key');
    if (empty($app_key)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = 'app_key不能为空';
      return;
    }

    $result = appDao::getAppRow($app_key);
    if (is_array($result)) {
      if (count($result) > 0) {
        $response->message = '获取应用详情成功';
        $response->data = $result[0];
      } else {
        $response->status = ErrorMsg::SPECIFIC_ERROR;
        $response->message = '获取应用详情为空';
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取应用详情失败'));
    }
	}

	private function getListAction (&$response) {
    $limit = (int)HttpRequestHelper::GetParam('limit');
    $offset = (int)HttpRequestHelper::GetParam('offset');

    $fields = HttpRequestHelper::GetParam('fieldsList');

    $fields = $fields ? explode(",",$fields) : 1 ;//如果有$fields则分割,没有默认为1
    $fields = $fields && is_array($fields) ? $fields : 1 ; //当空数组或非数字的时候,置为1

    $params = array();
    if (!empty($limit)) {
      $params['limit'] = $limit;
    }
    if (!empty($offset)) {
      $params['offset'] = $offset;
    }

    $result = appDao::getAppList($params, $fields);
    if (is_array($result['list'])) {
      if (count($result['list']) > 0) {
        $response->message = '获取应用列表成功';
        $response->data = $result;
      } else {
        $response->status = ErrorMsg::SPECIFIC_ERROR;
        $response->message = '获取应用列表为空';
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取应用列表失败'));
    }
	}
}
