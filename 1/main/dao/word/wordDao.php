<?php
require('cachedWordDaoImpl.php');

class wordDao extends DaoProxyBase {
	protected static $client_;

	public static function GetClient () {
		if (!isset(self::$client_)) {
      self::$client_ = new cachedWordDaoImpl();
    }
    return self::$client_;
	}
}
