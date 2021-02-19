<?php
require('cachedSongDaoImpl.php');

class songDao extends DaoProxyBase {
  protected static $client_;

  public static function GetClient () {
    if (!isset(self::$client_)) {
      self::$client_ = new cachedSongDaoImpl();
    }
    return self::$client_;
  }
}
