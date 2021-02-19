<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');
require(MAIN_ROOT . 'dao/auth/usergroup/usergroupDao.php');

class usergroupModel extends AjaxModel {

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
    $group_id = (int)HttpRequestHelper::GetParam('group_id');
    if (empty($group_id)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '用户组标识不能为空';
      return;
    }

    $fields = HttpRequestHelper::GetParam('fieldsList');

    $fields = $fields ? explode(",",$fields) : 1 ;//如果有$fields则分割,没有默认为1
    $fields = $fields && is_array($fields) ? $fields : 1 ; //当空数组或非数字的时候,置为1

    $result = usergroupDao::getUsergroupRow($group_id, $fields);
    if (is_array($result)) {
      if (count($result) > 0) {
        $group_id = $result[0]['group_id'];
        $group_name = $result[0]['group_name'];
        $group_info = $result[0]['group_info'];

        $result = usergroupDao::getActionToGroupRelation($group_id);
        if (is_array($result)) {
          if (count($result) > 0) {
            $actions = $result;

            $result = usergroupDao::getGroupToUserRelation($group_id);
            if (is_array($result)) {
              $response->message = '获取用户组详情成功';
              $response->data = array('group_id'=>$group_id, 'group_name'=>$group_name, 'group_info'=>$group_info, 'actions'=>$actions, 'users'=>$result);
            } else {
              ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取用户组映射关系失败'));
            }
          } else {
            $response->status = ErrorMsg::SPECIFIC_ERROR;
            $response->message = '获取权限映射关系为空';
          }
        } else {
          ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取权限映射关系失败'));
        }
      } else {
        $response->status = ErrorMsg::SPECIFIC_ERROR;
        $response->message = '获取用户组详情为空';
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取用户组详情失败'));
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

    $result = usergroupDao::getUsergroupList($params, $fields);
    if (is_array($result['list'])) {
      if (count($result['list']) > 0) {
        $response->message = '获取用户组列表成功';
        $response->data = $result;
      } else {
        $response->status = ErrorMsg::SPECIFIC_ERROR;
        $response->message = '获取用户组列表为空';
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取用户组列表失败'));
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
    
    if (empty($info['group_name'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '用户组名不能为空';
      return;
    }
    if (empty($info['group_info'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '用户组信息不能为空';
      return;
    }
    if (empty($info['actions'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '用户组权限不能为空';
      return;
    }

    $actions = $info['actions'];
    $actions = explode(',', $actions);
    unset($info['actions']);

    $uids = $info['uids'];
    if (empty($uids)) {
      $uids = array_filter(explode(',', $uids));
    } else {
      $uids = explode(',', $uids);
    }
    unset($info['uids']);

    $info['create_time'] = date('Y-m-d H:i:s', time());

    $info['operator_name'] = $this->userinfo['username'];

    $result = usergroupDao::createUsergroupRow($info);
    if ($result) {
      $group_id = $result;

      $result = usergroupDao::createActionToGroupRelation($group_id, $actions);
      if ($result) {
        if (count($uids) > 0) {
          $result = usergroupDao::createGroupToUserRelation($group_id, $uids);
          if ($result) {
            $result = array(
              'group_id' => $group_id
            );
            $response->message = '新建用户组成功';
            $response->data = $result;
          } else {
            ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('添加用户组映射关系失败'));
          }
        } else {
          $result = array(
            'group_id' => $group_id
          );
          $response->message = '新建用户组成功';
          $response->data = $result;
        }
      } else {
        ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('添加权限映射关系失败'));
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('新建用户组失败'));
    }
	}

	private function updateAction (&$response) {
    $group_id = (int)HttpRequestHelper::PostParam('group_id');
    if (empty($group_id)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '用户组标识不能为空';
      return;
    }

    $info = HttpRequestHelper::PostParam('info');
    $info = json_decode($info, true);
    if (empty($info)) {
      $response->status = ErrorMsg::REQUEST_PARAM_ERROR;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::REQUEST_PARAM_ERROR];
      return;
    }
    
    if (empty($info['group_name'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '用户组名不能为空';
      return;
    }
    if (empty($info['group_info'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '用户组信息不能为空';
      return;
    }
    if (empty($info['actions'])) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '用户组权限不能为空';
      return;
    }

    $actions = $info['actions'];
    $actions = explode(',', $actions);
    unset($info['actions']);

    $uids = $info['uids'];
    if (empty($uids)) {
      $uids = array_filter(explode(',', $uids));
    } else {
      $uids = explode(',', $uids);
    }
    unset($info['uids']);

    $info['operator_name'] = $this->userinfo['username'];

    $result = usergroupDao::updateUsergroupRow($group_id, $info);
    if ($result) {
      $result = usergroupDao::deleteActionToGroupRelation($group_id);
      if ($result) {
        $result = usergroupDao::createActionToGroupRelation($group_id, $actions);
        if ($result) {
          $result = usergroupDao::deleteGroupToUserRelation($group_id);
          if ($result) {
            if (count($uids) > 0) {
              $result = usergroupDao::createGroupToUserRelation($group_id, $uids);
              if ($result) {
                $response->message = '更新用户组详情成功';
              } else {
                ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('添加用户组映射关系失败'));
              }
            } else {
              $response->message = '更新用户组详情成功';
            }
          } else {
            ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('删除用户组映射关系失败'));
          }
        } else {
          ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('添加权限映射关系失败'));
        }
      } else {
        ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('删除权限映射关系失败'));
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('更新用户组详情失败'));
    }
	}

	private function deleteAction (&$response) {
		$group_id = (int)HttpRequestHelper::GetParam('group_id');
    if (empty($group_id)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '用户组标识不能为空';
      return;
    }

    $result = usergroupDao::deleteUsergroupRow($group_id);
    if ($result) {
      $result = usergroupDao::deleteActionToGroupRelation($group_id);
      if ($result) {
        $result = usergroupDao::deleteActionToGroupRelation($group_id);
        if ($result) {
          $response->message = '删除用户组成功';
        } else {
          ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('删除用户组映射关系失败'));
        }
      } else {
        ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('删除权限映射关系失败'));
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('删除用户组失败'));
    }
	}
}
