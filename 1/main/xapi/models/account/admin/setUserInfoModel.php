<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');

class setUserInfoModel extends AjaxModel {

	protected $need_login = true;

	public function GetResponse_ () {
		$response = new Response();

		$uid = HttpRequestHelper::PostParam('uid');
    if (empty($uid)) {
      $response->status = ErrorMsg::USER_UID_EMPTY;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_UID_EMPTY];
      return $response;
    }

    $usergroups = HttpRequestHelper::PostParam('usergroups');
    if (empty($usergroups)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '所属用户组不能为空';
      return;
    }

    $usergroups = explode(',', $usergroups);

    $result = accountDao::deleteGroupToUserRelation($uid);
    if ($result) {
    	$result = accountDao::addGroupToUserRelation($uid, $usergroups);
    	if ($result) {
    		$response->message = '更新用户信息成功';
    	} else {
    		ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('添加用户组映射关系失败'));
    	}
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('删除用户组映射关系失败'));
    }

    return $response;
	}
}
