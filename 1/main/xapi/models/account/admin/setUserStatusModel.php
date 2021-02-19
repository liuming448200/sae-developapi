<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');

class setUserStatusModel extends AjaxModel {

	protected $need_login = true;

	public function GetResponse_ () {
		$response = new Response();

		$uid = HttpRequestHelper::PostParam('uid');
    if (empty($uid)) {
      $response->status = ErrorMsg::USER_UID_EMPTY;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_UID_EMPTY];
      return $response;
    }

    $status = HttpRequestHelper::PostParam('status');
    if (!isset($status)) {
    	$response->status = ErrorMsg::SORRY_MESSAGE;
    	$response->message = '用户状态信息不能为空';
      return $response;
    }

    $result = accountDao::setUserStatus($uid, $status);
    if ($result) {
      $response->message = '更新用户状态成功';
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('更新用户状态失败'));
    }

    return $response;
	}
}
