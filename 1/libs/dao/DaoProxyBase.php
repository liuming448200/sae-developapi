<?php
require(PHP_ROOT . 'libs/dao/DaoProxyInterface.php');

abstract class DaoProxyBase implements DaoProxyInterface {
  private function __construct() {
  }

  // 阻止用户复制对象实例
  private function __clone() {
  }

  public static function __callStatic($method_name, $arguments) {
    $client = static::GetClient();
    return call_user_func_array(array($client, $method_name), $arguments);
  }
}

