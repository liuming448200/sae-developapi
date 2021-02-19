<?php
require('cachedUsergroupDaoImpl.php');

class usergroupDao extends DaoProxyBase {
	protected static $client_;

	public static function GetClient () {
    if (!isset(self::$client_)) {
      self::$client_ = new cachedUsergroupDaoImpl();
    }
    return self::$client_;
  }
}
