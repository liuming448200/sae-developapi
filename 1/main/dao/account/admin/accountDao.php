<?php
require('cachedAccountDaoImpl.php');

class accountDao extends DaoProxyBase {
	protected static $client_;

	public static function GetClient () {
		if (!isset(self::$client_)) {
      self::$client_ = new cachedAccountDaoImpl();
    }
    return self::$client_;
	}
}
