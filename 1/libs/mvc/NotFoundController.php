<?php
require PHP_ROOT . 'libs/mvc/ControllerBase.php';
require PHP_ROOT . 'libs/mvc/SmartyView.php';

/**
 * 定义默认的未找到路径的处理:直接跳转回首页
 *   该controller会被 libs/mvc/UrlRouter.php 调用，作为默认404路由值。
 */
class NotFoundController extends ControllerBase {
  public function Run() {
    $view = new SmartyView();
    $view->SetHeader('HTTP/1.1 404 Not Found');
  }
}
