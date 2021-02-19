<?php
require('cachedStoryDaoImpl.php');

class storyDao extends DaoProxyBase {
  protected static $client_;

  public static function GetClient () {
    if (!isset(self::$client_)) {
      self::$client_ = new cachedStoryDaoImpl();
    }
    return self::$client_;
  }
}
