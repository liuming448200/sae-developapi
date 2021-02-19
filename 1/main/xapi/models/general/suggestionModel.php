<?php
require(WEB_ROOT . 'models/extra/AjaxModel.php');
require(MAIN_ROOT . 'dao/general/suggestion/suggestionDao.php');

class suggestionModel extends AjaxModel {

  public function GetResponse_ () {
    $response = new Response();

    $content = trim(HttpRequestHelper::PostParam('content'));
    if (empty($content)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '建议内容不能为空';
      return $response;
    }

    $contact = trim(HttpRequestHelper::PostParam('contact'));
    if (empty($contact)) {
      $response->status = ErrorMsg::SORRY_MESSAGE;
      $response->message = '联系方式不能为空';
      return $response;
    }

    $result = suggestionDao::createSuggestionRow($content, $contact);
    if ($result) {
      $response->message = '感谢您提交的宝贵建议，我们将认真对待，谢谢';
    } else {
      ErrorMsg::FillResponseAndLog($response, ErrorMsg::ERROR_MESSAGE, array('提交失败，请重新提交'));
    }

    return $response;
  }
}
