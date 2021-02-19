<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');

class changeMobileModel extends AjaxModel {

    protected $need_login = true;

	public function GetResponse_ () {
		$response = new Response();

		$uid = $this->userinfo['uid'];

		$mobile = trim(HttpRequestHelper::PostParam('mobile'));
		if (empty($mobile)) {
            $response->status = ErrorMsg::USER_MOBILE_EMPTY;
            $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_MOBILE_EMPTY];
            return $response;
        } else if (!Utility::ValidateIsMobile($mobile)) {
            $response->status = ErrorMsg::USER_MOBILE_ERROR;
            $response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_MOBILE_ERROR];
            return $response;
        }

        $result = accountDao::changeMobile($uid, $mobile);
        if ($result) {
        	$response->message = '更换手机号成功';
        } else {
        	ErrorMsg::FillResponseAndLog($response, ErrorMsg::USER_MOBILE_UPDATE_FAILED);
        }

        return $response;
	}
}
