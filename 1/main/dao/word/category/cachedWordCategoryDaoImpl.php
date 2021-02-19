<?php
require('wordCategoryDaoImpl.php');

class cachedWordCategoryDaoImpl extends wordCategoryDaoImpl {
	const MEMCACHE_GROUP = 'default';
  const CACHE_EXPIRE = 86400;

  const CACHE_WORD_CATEGORY_PREFIX = 'word-category-info-';

  private function getCacheKey ($categoryId) {
    return self::CACHE_WORD_CATEGORY_PREFIX . $categoryId;
  }

  public function getWordCategoryRow ($categoryId, $fields) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($categoryId);
    $cached_result = $memcached_client->get($cache_key);
    if ($cached_result) {
        return $cached_result;
    }
    $db_result = parent::getWordCategoryRow($categoryId, $fields);
    if ($db_result) {
        $memcached_client->add($cache_key, $db_result, self::CACHE_EXPIRE);
    }
    return $db_result;
  }

  public function updateWordCategoryRow ($categoryId, $info) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($categoryId);
    $memcached_client->delete($cache_key);
    $db_result = parent::updateWordCategoryRow($categoryId, $info);
    return $db_result;
  }

  public function deleteWordCategoryRow ($categoryId) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($categoryId);
    $memcached_client->delete($cache_key);
    $db_result = parent::deleteWordCategoryRow($categoryId);
    return $db_result;
  }
}
