<?php
require('cachedActionDaoImpl.php');

class actionDao extends DaoProxyBase {
	protected static $client_;

	public static function GetClient () {
    if (!isset(self::$client_)) {
      self::$client_ = new cachedActionDaoImpl();
    }
    return self::$client_;
  }
}
