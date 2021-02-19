<?php
require('suggestionDaoImpl.php');

class suggestionDao extends DaoProxyBase {
  protected static $client_;

  public static function GetClient () {
    if (!isset(self::$client_)) {
      self::$client_ = new suggestionDaoImpl();
    }
    return self::$client_;
  }
}
