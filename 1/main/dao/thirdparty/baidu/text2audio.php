<?php
require('cachedText2audioImpl.php');

class text2audio extends DaoProxyBase {
	protected static $client_;

	public static function GetClient () {
		if (!isset(self::$client_)) {
      self::$client_ = new cachedText2audioImpl();
    }
    return self::$client_;
	}
}
