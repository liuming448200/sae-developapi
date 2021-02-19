<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');

class userInfoModel extends AjaxModel {

    const KEYWORD_TYPE_MERMBER_ID = 0; // 会员ID
    const KEYWORD_TYPE_MOBILE     = 1; // 手机号

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
                if (self::KEYWORD_TYPE_MERMBER_ID == $keywordType) {
                    $uid = $result[0]['uid'];
                    $mobile = $result[0]['mobile'];
                    $username = $result[0]['username'];
                    $extra = accountMoreDao::getUserInfoMoreRow($uid);
                    if (is_array($extra)) {
                      $child = childAccountDao::getChildInfoRow($uid);
                      if (is_array($child)) {
                        $response->message = '用户登录成功';
                        if (count($extra) > 0 && count($child) > 0) {
                          $response->data = array('uid'=>$uid, 'mobile'=>$mobile, 'username'=>$username, 'extra'=>$extra[0], 'child'=>$child[0]);
                        } else if (count($extra) > 0) {
                          $response->data = array('uid'=>$uid, 'mobile'=>$mobile, 'username'=>$username, 'extra'=>$extra[0]);
                        } else if (count($child) > 0) {
                          $response->data = array('uid'=>$uid, 'mobile'=>$mobile, 'username'=>$username, 'child'=>$child[0]);
                        } else {
                          $response->data = array('uid'=>$uid, 'mobile'=>$mobile, 'username'=>$username);
                        }
                      } else {
                        ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取孩子基本信息失败'));
                      }
                    } else {
                      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('获取用户详情失败'));
                    }
                } else if (self::KEYWORD_TYPE_MOBILE == $keywordType) {
                    $response->message = '用户信息请求成功';
                    $response->data = $result[0];
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
