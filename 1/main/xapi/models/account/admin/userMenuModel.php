<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');

class userMenuModel extends AjaxModel {

	protected $need_login = true;

	public function GetResponse_ () {
		$response = new Response();

		$usergroups = HttpRequestHelper::GetParam('usergroups');
		if (empty($usergroups)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '用户所属的用户组不能为空';
      return $response;
    }

    $result = accountDao::getUserMenu($usergroups);
    if (is_array($result)) {
      if (count($result) > 0) {
        $response->message = '获取用户菜单成功';
        $response->data = $result;
      } else {
        $response->status = ErrorMsg::SPECIFIC_ERROR;
        $response->message = '获取用户菜单为空';
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取用户菜单失败'));
    }

    return $response;
	}
}
