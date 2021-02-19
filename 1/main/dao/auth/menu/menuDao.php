<?php
require('cachedMenuDaoImpl.php');

class menuDao extends DaoProxyBase {
	protected static $client_;

	public static function GetClient () {
    if (!isset(self::$client_)) {
      self::$client_ = new cachedMenuDaoImpl();
    }
    return self::$client_;
  }
}
