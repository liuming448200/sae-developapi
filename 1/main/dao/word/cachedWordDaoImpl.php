<?php
require('wordDaoImpl.php');

class cachedWordDaoImpl extends wordDaoImpl {
	const MEMCACHE_GROUP = 'default';
  const CACHE_EXPIRE = 86400;

  const CACHE_WORD_INFO_PREFIX = 'word-info-';

  private function getCacheKey ($wordId) {
    return self::CACHE_WORD_INFO_PREFIX . $wordId;
  }

  public function getWordRow ($wordId, $fields) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($wordId);
    $cached_result = $memcached_client->get($cache_key);
    if ($cached_result) {
        return $cached_result;
    }
    $db_result = parent::getWordRow($wordId, $fields);
    if ($db_result) {
        $memcached_client->add($cache_key, $db_result, self::CACHE_EXPIRE);
    }
    return $db_result;
  }

  public function updateWordRow ($wordId, $info) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($wordId);
    $memcached_client->delete($cache_key);
    $db_result = parent::updateWordRow($wordId, $info);
    return $db_result;
  }

  public function deleteWordRow ($wordId) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($wordId);
    $memcached_client->delete($cache_key);
    $db_result = parent::deleteWordRow($wordId);
    return $db_result;
  }
}
