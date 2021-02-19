<?php
require('cachedSearchDaoImpl.php');

class searchDao extends DaoProxyBase {
	protected static $client_;

  public static function GetClient () {
    if (!isset(self::$client_)) {
      self::$client_ = new cachedSearchDaoImpl();
    }
    return self::$client_;
  }
}
