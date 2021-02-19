<?php
require(PHP_ROOT . 'libs/mvc/UrlRouter.php');

class CustomUrlRouter extends UrlRouter {

  protected function GetNotFoundController () {
    return 'NotFoundController';
  }
}

$url_router = new CustomUrlRouter();
$router_list = RouterMap::$router;
$url_router->SetMapping($router_list);

$url_router->SetControllerPath(WEB_ROOT . 'controllers');
$url_router->Route();
