<?php
/**
 * 所有controller的父类
 */
require(PHP_ROOT . 'libs/mvc/ControllerBase.php');

abstract class WebAjaxController extends ControllerBase {

  protected $isJsonpRequest = false;
  protected $viewHandler = null;

  protected function SeeError ($response) {
    $this->viewHandler->SetData($response);
    $this->viewHandler->Display();
  }

  abstract protected function GetResponse_();

  public function Run () {
    $response = $this->GetResponse_();

    if ($this->isJsonpRequest) {
      require(PHP_ROOT . 'libs/mvc/JsonpView.php');
      $this->viewHandler = new JsonpView();
    } else {
      require(PHP_ROOT . 'libs/mvc/JsonView.php');
      $this->viewHandler = new JsonView();
    }

    if (200 !== $response->status) {
      $this->SeeError($response);
      return;
    }

    $this->viewHandler->SetData($response);
    $this->viewHandler->Display();
  }

  /**
   * jsonp请求，需要设置该变量.
   */
  public function setJsonpRequest () {
    $this->isJsonpRequest = true;
  }
}
