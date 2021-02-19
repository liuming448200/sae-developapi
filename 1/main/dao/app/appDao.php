<?php
require(PHP_ROOT . 'libs/dao/DaoProxyBase.php');
require('cachedAppDaoImpl.php');

class appDao extends DaoProxyBase {
	protected static $client_;

	public static function GetClient () {
		if (!isset(self::$client_)) {
      self::$client_ = new cachedAppDaoImpl();
    }
    return self::$client_;
	}
}
