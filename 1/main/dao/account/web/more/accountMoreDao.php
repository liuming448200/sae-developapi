<?php
require('cachedAccountMoreDaoImpl.php');

class accountMoreDao extends DaoProxyBase {
  protected static $client_;

  public static function GetClient () {
    if (!isset(self::$client_)) {
      self::$client_ = new cachedAccountMoreDaoImpl();
    }
    return self::$client_;
  }
}
