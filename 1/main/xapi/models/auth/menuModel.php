<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');
require(MAIN_ROOT . 'dao/auth/menu/menuDao.php');

class menuModel extends AjaxModel {

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
      case 'create':
        $this->createAction($response);
        break;
      case 'update':
        $this->updateAction($response);
        break;
      case 'delete':
        $this->deleteAction($response);
        break;
      default:
        $response->status = ErrorMsg::REQUEST_URL_ERROR;
        $response->message = ErrorMsg::$error_msg_array[ErrorMsg::REQUEST_URL_ERROR];
        break;
    }

    return $response;
	}

	private function getAction (&$response) {
		$menu_id = (int)HttpRequestHelper::GetParam('menu_id');
		if (empty($menu_id)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '菜单标识不能为空';
      return;
    }

    $fields = HttpRequestHelper::GetParam('fieldsList');

    $fields = $fields ? explode(",",$fields) : 1 ;//如果有$fields则分割,没有默认为1
    $fields = $fields && is_array($fields) ? $fields : 1 ; //当空数组或非数字的时候,置为1

    $result = menuDao::getMenuRow($menu_id, $fields);
    if (is_array($result)) {
      if (count($result) > 0) {
        $response->message = '获取菜单详情成功';
        $response->data = $result[0];
      } else {
        $response->status = ErrorMsg::SPECIFIC_ERROR;
        $response->message = '获取菜单详情为空';
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取菜单详情失败'));
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

    $result = menuDao::getMenuList($params, $fields);
    if (is_array($result['list'])) {
      if (count($result['list']) > 0) {
        $response->message = '获取菜单列表成功';
        $response->data = $result;
      } else {
        $response->status = ErrorMsg::SPECIFIC_ERROR;
        $response->message = '获取菜单列表为空';
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取菜单列表失败'));
    }
	}

	private function createAction (&$response) {
		$info = HttpRequestHelper::PostParam('info');
    $info = json_decode($info, true);
    if (empty($info)) {
      $response->status = ErrorMsg::REQUEST_PARAM_ERROR;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::REQUEST_PARAM_ERROR];
      return;
    }
    
    if (empty($info['tid'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '内容标识不能为空';
      return;
    }
    if (empty($info['menu_name'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '菜单名称不能为空';
      return;
    }

    $info['create_time'] = date('Y-m-d H:i:s', time());

    $info['operator_name'] = $this->userinfo['username'];

    $result = menuDao::createMenuRow($info);
    if ($result) {
      $result = array(
        'menu_id' => $result
      );
      $response->message = '新建菜单成功';
      $response->data = $result;
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('新建菜单失败'));
    }
	}

	private function updateAction (&$response) {
		$menu_id = (int)HttpRequestHelper::PostParam('menu_id');
		if (empty($menu_id)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '菜单标识不能为空';
      return;
    }

    $info = HttpRequestHelper::PostParam('info');
    $info = json_decode($info, true);
    if (empty($info)) {
      $response->status = ErrorMsg::REQUEST_PARAM_ERROR;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::REQUEST_PARAM_ERROR];
      return;
    }
    
    if (empty($info['tid'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '内容标识不能为空';
      return;
    }
    if (empty($info['menu_name'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '菜单名称不能为空';
      return;
    }

    $info['operator_name'] = $this->userinfo['username'];

    $result = menuDao::updateMenuRow($menu_id, $info);
    if ($result) {
      $response->message = '更新菜单详情成功';
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('更新菜单详情失败'));
    }
	}

	private function deleteAction (&$response) {
		$menu_id = (int)HttpRequestHelper::GetParam('menu_id');
    if (empty($menu_id)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '菜单标识不能为空';
      return;
    }

    $result = menuDao::deleteMenuRow($menu_id);
    if ($result) {
      $response->message = '删除菜单成功';
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('删除菜单失败'));
    }
	}
}
