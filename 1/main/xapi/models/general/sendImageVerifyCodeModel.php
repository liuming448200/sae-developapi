<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');
require(WEB_ROOT . 'models/extra/ImageVcode.php');

class sendImageVerifyCodeModel extends AjaxModel {

	public function GetResponse_ () {
		$identity = HttpRequestHelper::GetParam('identity');
		if (empty($identity)) {
			$response = new Response();
			$response->status = ErrorMsg::SORRY_MESSAGE;
			$response->message = '图片验证码标识符不能为空';
			return $response;
		}

		ImageVcode::getImageVerifyCode($identity);
		exit;
	}
}
