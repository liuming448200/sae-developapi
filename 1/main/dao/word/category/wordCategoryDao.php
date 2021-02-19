<?php
require('cachedWordCategoryDaoImpl.php');

class wordCategoryDao extends DaoProxyBase {
	protected static $client_;

	public static function GetClient () {
		if (!isset(self::$client_)) {
      self::$client_ = new cachedWordCategoryDaoImpl();
    }
    return self::$client_;
	}
}
