<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');
require(MAIN_ROOT . 'dao/content/contentDao.php');

class contentBaseInfoModel extends AjaxModel {

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
		$tid = (int)HttpRequestHelper::GetParam('tid');
    if (empty($tid)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '内容标识不能为空';
      return;
    }

    $fields = HttpRequestHelper::GetParam('fieldsList');

    $fields = $fields ? explode(",",$fields) : 1 ;//如果有$fields则分割,没有默认为1
    $fields = $fields && is_array($fields) ? $fields : 1 ; //当空数组或非数字的时候,置为1

    $result = contentDao::getContentRow($tid, $fields);
    if (is_array($result)) {
      if (count($result) > 0) {
        $response->message = '获取内容详情成功';
        $response->data = $result[0];
      } else {
        $response->status = ErrorMsg::SPECIFIC_ERROR;
        $response->message = '获取内容详情为空';
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取内容详情失败'));
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

    $result = contentDao::getContentList($params, $fields);
    if (is_array($result['list'])) {
      if (count($result['list']) > 0) {
        $response->message = '获取内容列表成功';
        $response->data = $result;
      } else {
        $response->status = ErrorMsg::SPECIFIC_ERROR;
        $response->message = '获取内容列表为空';
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取内容列表失败'));
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
    
    if (empty($info['typename'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '内容名称不能为空';
      return;
    }

    $info['create_time'] = date('Y-m-d H:i:s', time());

    $result = contentDao::createContentRow($info);
    if ($result) {
      $result = array(
        'tid' => $result
      );
      $response->message = '新建内容成功';
      $response->data = $result;
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('新建内容失败'));
    }
	}

	private function updateAction (&$response) {
		$tid = (int)HttpRequestHelper::PostParam('tid');
    if (empty($tid)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '内容标识不能为空';
      return;
    }

    $info = HttpRequestHelper::PostParam('info');
    $info = json_decode($info, true);
    if (empty($info)) {
      $response->status = ErrorMsg::REQUEST_PARAM_ERROR;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::REQUEST_PARAM_ERROR];
      return;
    }
    
    if (empty($info['typename'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '内容名称不能为空';
      return;
    }

    $result = contentDao::updateContentRow($tid, $info);
    if ($result) {
      $response->message = '更新内容详情成功';
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('更新内容详情失败'));
    }
	}

	private function deleteAction (&$response) {
		$tid = (int)HttpRequestHelper::GetParam('tid');
    if (empty($tid)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '内容标识不能为空';
      return;
    }

    $result = contentDao::deleteContentRow($tid);
    if ($result) {
      $response->message = '删除内容成功';
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('删除内容失败'));
    }
	}
}
