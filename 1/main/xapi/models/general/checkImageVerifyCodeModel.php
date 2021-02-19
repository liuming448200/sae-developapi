<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');
require(WEB_ROOT . 'models/extra/ImageVcode.php');

class checkImageVerifyCodeModel extends AjaxModel {

	public function GetResponse_ () {
		$response = new Response();

		$identity = HttpRequestHelper::GetParam('identity');
		if (empty($identity)) {
			$response->status = ErrorMsg::SORRY_MESSAGE;
			$response->message = '图片验证码标识符不能为空';
			return $response;
		}

		$verifyCode = trim(HttpRequestHelper::GetParam('verifyCode'));
		if (empty($verifyCode)) {
			$response->status = ErrorMsg::USER_AUTHCODE_EMPTY;
			$response->message = ErrorMsg::$error_msg_array[ErrorMsg::USER_AUTHCODE_EMPTY];
      return $response;
    }

    $result = ImageVcode::checkImageVerifyCode($identity, $verifyCode);
    if ($result) {
    	$response->message = '图形验证码验证通过';
    } else {
    	ErrorMsg::FillResponseAndLog($response, ErrorMsg::ANTISPAM_IMG_CODE_ERROR);
    }

    return $response;
	}
}
