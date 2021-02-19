<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');

class LogoutModel extends AjaxModel {

	protected $need_login = true;

	public function GetResponse_ () {
		$response = new Response();

    $accessToken = HttpRequestHelper::GetParam('accessToken');
    if (empty($accessToken)) {
      $response->status = ErrorMsg::ACCESS_TOKEN_EMPTY;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::ACCESS_TOKEN_EMPTY];
      return $response;
    }

		$uid = HttpRequestHelper::GetParam('uid');
    if (empty($uid)) {
      $response->status = ErrorMsg::USER_UID_EMPTY;
      $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_UID_EMPTY];
      return $response;
    }

    $result = accountDao::removeAccessToken($accessToken, $uid);
    if ($result) {
      $response->message = 'accessToken删除成功';
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('accessToken删除失败'));
    }

    return $response;
	}
}
