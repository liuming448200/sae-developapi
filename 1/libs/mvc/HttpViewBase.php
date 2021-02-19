<?php
/**
 * HttpViewBase是一个View基类，对于Http相关的一些操作进行约定和封装
 * 其他子类可以通过继承该类来进行扩展
 */

require PHP_ROOT . 'libs/mvc/ViewBase.php';

abstract class HttpViewBase extends ViewBase {
  /**
   * 修改http header
   * @param new_header string [必须]需要设置的新http header
   */
  public function SetHeader($new_header) {
    if (!empty($new_header)) {
      header($new_header);
    }
  }

  /**
   * 重定向，请统一使用这个函数进行重定向操作
   * @param new_url string [必须]需要重定向的新url
   */
  public function Redirect($new_url) {
    if (!empty($new_url)) {
      header("Location: $new_url");
    }
  }

  public function OutputNotFound() {
    $this->SetHeader("HTTP/1.1 404 Not Found");
    $this->SetHeader("status: 404 Not Found");
  }
}
