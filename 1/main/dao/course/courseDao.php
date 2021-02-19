<?php
require('cachedCourseDaoImpl.php');

class courseDao extends DaoProxyBase {
	protected static $client_;

	public static function GetClient () {
		if (!isset(self::$client_)) {
      self::$client_ = new cachedCourseDaoImpl();
    }
    return self::$client_;
	}
}
