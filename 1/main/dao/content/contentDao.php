<?php
require('cachedContentDaoImpl.php');

class contentDao extends DaoProxyBase {
	protected static $client_;

	public static function GetClient () {
    if (!isset(self::$client_)) {
      self::$client_ = new cachedContentDaoImpl();
    }
    return self::$client_;
  }
}
