<?php
require('Cookie.php');
require('SessionMemCachedClient.php');

class Session {
  const SESSION_PREFIX = "SESS_";
  // singleton
  private static $session_handler_;

  public static function Init() {
    if (!isset(self::$session_handler_))
      self::$session_handler_ = new Session();
  }

  // 阻止用户复制对象实例
  public function __clone() {
    trigger_error('Clone Session is not allowed.', E_USER_ERROR);
  }

  private function __construct() {
    session_set_save_handler(array($this, "Open"),
                             array($this, "Close"),
                             array($this, "Read"),
                             array($this, "Write"),
                             array($this, "Destroy"),
                             array($this, "Gc"));
    // for web user
    ini_set("session.name", "SESSIONID");
    ini_set("session.use_cookies", "0");
    $session_id = Cookie::Get(SESSIONID);
    if (!empty($session_id)) {
      session_id($session_id);
    }
    session_start();
  }

  public function __destruct() {
    session_write_close();
  }

  public function Open($save_path, $session_name) {
    return true;
  }

  public function Close() {
    return true;
  }

  public function Read($session_id) {
    $key = self::SESSION_PREFIX . $session_id;
    $memcached_client_ = SessionMemCachedClient::GetInstance();
    return (string)$memcached_client_->get($key);
  }

  public function Write($session_id, $data) {
    $key = self::SESSION_PREFIX . $session_id;
    $memcached_client_ = SessionMemCachedClient::GetInstance();
    if (0 == SESSION_EXPIRE_TIME) {
      return $memcached_client_->set($key, $data, SESSION_MAX_TIME);
    } else {
      return $memcached_client_->set($key, $data, SESSION_EXPIRE_TIME);
    }
  }

  public function Destroy($session_id) {
    $key = self::SESSION_PREFIX . $session_id;
    $memcached_client_ = SessionMemCachedClient::GetInstance();
    return $memcached_client_->delete($key);
  }

  public function Gc($maxlifetime) {
    return true;
  }
}

// Session::Init();
