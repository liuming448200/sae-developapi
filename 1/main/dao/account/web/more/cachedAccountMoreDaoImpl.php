<?php
require('accountMoreDaoImpl.php');

class cachedAccountMoreDaoImpl extends accountMoreDaoImpl {
  const MEMCACHE_GROUP = 'default';
  const CACHE_EXPIRE = 86400;

  const CACHE_WEB_ACCOUNT_MORE_PREFIX = 'web-account-more-';

  private function getCacheKey ($keyword) {
    return self::CACHE_WEB_ACCOUNT_MORE_PREFIX . $keyword;
  }

  public function getUserInfoMoreRow ($uid, $fields = 1) {
    $memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($uid);
    $cached_result = $memcached_client->get($cache_key);
    if ($cached_result) {
      return $cached_result;
    }
    $db_result = parent::getUserInfoMoreRow($uid, $fields);
    if ($db_result) {
      $memcached_client->add($cache_key, $db_result, self::CACHE_EXPIRE);
    }
    return $db_result;
  }

  public function updateUserInfoMoreRow ($uid, $info) {
    $memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($uid);
    $memcached_client->delete($cache_key);
    $db_result = parent::updateUserInfoMoreRow($uid, $info);
    return $db_result;
  }

  public function deleteUserInfoMoreRow ($uid) {
    $memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($uid);
    $memcached_client->delete($cache_key);
    $db_result = parent::deleteUserInfoMoreRow($uid);
    return $db_result;
  }
}
