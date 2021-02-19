<?php
require('cachedActivityDaoImpl.php');

class activityDao extends DaoProxyBase {
	protected static $client_;

	public static function GetClient () {
		if (!isset(self::$client_)) {
      self::$client_ = new cachedActivityDaoImpl();
    }
    return self::$client_;
	}
}
