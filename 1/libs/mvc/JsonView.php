<?php
/**
 * JsonView是一个View类，输出Json格式的http响应
 */

require PHP_ROOT . 'libs/mvc/HttpViewBase.php';

class JsonView extends HttpViewBase {
  protected $data_;
  public function __construct() {
  }

  public function SetData(&$data) {
    $this->data_ = $data;
  }

  public function Display() {
    $result = json_encode($this->data_);
    $this->SetHeader("Content-Type: application/json; charset=UTF-8");
    echo $result;
  }
}
