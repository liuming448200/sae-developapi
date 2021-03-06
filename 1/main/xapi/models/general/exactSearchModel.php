<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');
require(MAIN_ROOT . 'dao/general/search/searchDao.php');

class exactSearchModel extends AjaxModel {

	public function GetResponse_ () {
		$response = new Response();

		$keyword = trim(HttpRequestHelper::GetParam('keyword'));
    if (empty($keyword)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '搜索关键字不能为空';
      return $response;
    }

    $result = searchDao::getExactResult($keyword);
    if (is_array($result)) {
      if (count($result) > 0) {
        $response->message = '精确搜索成功';
        $response->data = $result;
      } else {
        $response->status = ErrorMsg::SPECIFIC_ERROR;
        $response->message = '精确搜索结果为空';
      }
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('精确搜索失败'));
    }

		return $response;
	}
}
