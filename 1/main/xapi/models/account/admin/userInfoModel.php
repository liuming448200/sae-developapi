<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');

class userInfoModel extends AjaxModel {

	protected $need_login = true;

	public function GetResponse_ () {
		$response = new Response();

        $keywordType = HttpRequestHelper::GetParam('keywordType');
        if (!isset($keywordType)) {
            $response->status = ErrorMsg::SORRY_MESSAGE;
            $response->message = '请求用户信息的关键字类型不能为空';
            return $response;
        }

        $urls = parse_url($_SERVER['REQUEST_URI']);
        $path = $urls['path'];
        if (!empty($path)) {
          $paths = explode('/', $path);
        }
        $len = count($paths);
        $keyword = $paths[$len-1];

        $result = accountDao::getUserInfo($keyword, $keywordType);
        if (is_array($result)) {
            if (count($result) > 0) {
                $uid = $result[0]['uid'];
                $mobile = $result[0]['mobile'];
                $username = $result[0]['username'];
                $status = $result[0]['status'];

                $result = accountDao::getGroupToUserRelation($uid);
                if (is_array($result)) {
                  if (count($result) > 0) {
                    $response->message = '用户信息请求成功';
                    $response->data = array('uid'=>$uid, 'mobile'=>$mobile, 'username'=>$username, 'status'=>$status, 'usergroups'=>$result);
                  } else {
                    $response->status = ErrorMsg::SPECIFIC_ERROR;
                    $response->message = '获取用户组映射关系为空';
                  }
                } else {
                  ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取用户组映射关系失败'));
                }
            } else {
                $response->status = ErrorMsg::SPECIFIC_ERROR;
                $response->message = '用户信息请求为空';
            }
        } else {
            ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('用户信息请求失败'));
        }

        return $response;
	}
}
