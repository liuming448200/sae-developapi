<?php
require('searchDaoImpl.php');

class cachedSearchDaoImpl extends searchDaoImpl {
	const MEMCACHE_GROUP = 'default';
  const CACHE_EXPIRE = 86400;

  const CACHE_RELATED_SEARCH_PREFIX = 'related-search-';
  const CACHE_EXACT_SEARCH_PREFIX = 'exact-search-';

  private function getCacheKey ($type, $keyword) {
    if (self::CACHE_RELATED_SEARCH_PREFIX == $type) {
      return self::CACHE_RELATED_SEARCH_PREFIX . $keyword;
    } else if (self::CACHE_EXACT_SEARCH_PREFIX == $type) {
      return self::CACHE_EXACT_SEARCH_PREFIX . $keyword;
    }
  }

  public function getRelatedResult ($keyword) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey(self::CACHE_RELATED_SEARCH_PREFIX, $keyword);
    $cached_result = $memcached_client->get($cache_key);
    if ($cached_result) {
        return $cached_result;
    }
    $db_result = parent::getRelatedResult($keyword);
    if ($db_result) {
        $memcached_client->add($cache_key, $db_result, self::CACHE_EXPIRE);
    }
    return $db_result;
  }

  public function getExactResult ($keyword) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey(self::CACHE_EXACT_SEARCH_PREFIX, $keyword);
    $cached_result = $memcached_client->get($cache_key);
    if ($cached_result) {
        return $cached_result;
    }
    $db_result = parent::getExactResult($keyword);
    if ($db_result) {
        $memcached_client->add($cache_key, $db_result, self::CACHE_EXPIRE);
    }
    return $db_result;
  }
}
