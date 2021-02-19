<?php
require('cachedChildAccountDaoImpl.php');

class childAccountDao extends DaoProxyBase {
  protected static $client_;

  public static function GetClient () {
    if (!isset(self::$client_)) {
      self::$client_ = new cachedChildAccountDaoImpl();
    }
    return self::$client_;
  }
}
