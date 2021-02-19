<?php
require('contentDaoImpl.php');

class cachedContentDaoImpl extends contentDaoImpl {
	const MEMCACHE_GROUP = 'default';
	const CACHE_EXPIRE = 86400;

	const CACHE_CONTENT_TYPE_PREFIX = 'content-type-';

	private function getCacheKey ($tid) {
    return self::CACHE_CONTENT_TYPE_PREFIX . $tid;
  }

  public function getContentRow ($tid, $fields) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($tid);
    $cached_result = $memcached_client->get($cache_key);
    if ($cached_result) {
        return $cached_result;
    }
    $db_result = parent::getContentRow($tid, $fields);
    if ($db_result) {
        $memcached_client->add($cache_key, $db_result, self::CACHE_EXPIRE);
    }
    return $db_result;
  }

  public function updateContentRow ($tid, $info) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($tid);
    $memcached_client->delete($cache_key);
    $db_result = parent::updateContentRow($tid, $info);
    return $db_result;
  }

  public function deleteContentRow ($tid) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($tid);
    $memcached_client->delete($cache_key);
    $db_result = parent::deleteContentRow($tid);
    return $db_result;
  }
}
