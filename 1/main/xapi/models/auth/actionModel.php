<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');
require(MAIN_ROOT . 'dao/auth/action/actionDao.php');

class actionModel extends AjaxModel {

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
    $action_id = (int)HttpRequestHelper::GetParam('action_id');
    if (empty($action_id)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '权限标识不能为空';
      return;
    }

    $fields = HttpRequestHelper::GetParam('fieldsList');

    $fields = $fields ? explode(",",$fields) : 1 ;//如果有$fields则分割,没有默认为1
    $fields = $fields && is_array($fields) ? $fields : 1 ; //当空数组或非数字的时候,置为1

    $result = actionDao::getActionRow($action_id, $fields);
    if (is_array($result)) {
      if (count($result) > 0) {
        $response->message = '获取权限详情成功';
        $response->data = $result[0];
      } else {
        $response->status = ErrorMsg::SPECIFIC_ERROR;
        $response->message = '获取权限详情为空';
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取权限详情失败'));
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

    $result = actionDao::getActionList($params, $fields);
    if (is_array($result['list'])) {
      if (count($result['list']) > 0) {
        $response->message = '获取权限列表成功';
        $response->data = $result;
      } else {
        $response->status = ErrorMsg::SPECIFIC_ERROR;
        $response->message = '获取权限列表为空';
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取权限列表失败'));
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
    
    if (empty($info['menu_id'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '菜单标识不能为空';
      return;
    }
    if (empty($info['action_name'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '权限名称不能为空';
      return;
    }
    if (empty($info['action'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '权限字符串不能为空';
      return;
    }

    $info['create_time'] = date('Y-m-d H:i:s', time());

    $info['operator_name'] = $this->userinfo['username'];

    $result = actionDao::createAcitonRow($info);
    if ($result) {
      $result = array(
        'action_id' => $result
      );
      $response->message = '新建权限成功';
      $response->data = $result;
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('新建权限失败'));
    }
	}

	private function updateAction (&$response) {
    $action_id = (int)HttpRequestHelper::PostParam('action_id');
    if (empty($action_id)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '权限标识不能为空';
      return;
    }

    $info = HttpRequestHelper::PostParam('info');
    $info = json_decode($info, true);
    if (empty($info)) {
      $response->status = ErrorMsg::REQUEST_PARAM_ERROR;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::REQUEST_PARAM_ERROR];
      return;
    }
    
    if (empty($info['menu_id'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '菜单标识不能为空';
      return;
    }
    if (empty($info['action_name'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '权限名称不能为空';
      return;
    }
    if (empty($info['action'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '权限字符串不能为空';
      return;
    }

    $info['operator_name'] = $this->userinfo['username'];

    $result = actionDao::updateActionRow($action_id, $info);
    if ($result) {
      $response->message = '更新权限详情成功';
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('更新权限详情失败'));
    }
	}

	private function deleteAction (&$response) {
		$action_id = (int)HttpRequestHelper::GetParam('action_id');
    if (empty($action_id)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '权限标识不能为空';
      return;
    }

    $result = actionDao::deleteActionRow($action_id);
    if ($result) {
      $response->message = '删除权限成功';
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('删除权限失败'));
    }
	}
}
