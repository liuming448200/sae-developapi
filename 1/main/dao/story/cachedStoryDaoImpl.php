<?php
require('storyDaoImpl.php');

class cachedStoryDaoImpl extends storyDaoImpl {
  const MEMCACHE_GROUP = 'default';
  const CACHE_EXPIRE = 86400;

  const CACHE_STORY_INFO_PREFIX = 'story-info-';

  private function getCacheKey ($storyId) {
    return self::CACHE_STORY_INFO_PREFIX . $storyId;
  }
  
  public function getStoryRow ($storyId, $fields) {
    $memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($storyId);
    $cached_result = $memcached_client->get($cache_key);
    if ($cached_result) {
        return $cached_result;
    }
    $db_result = parent::getStoryRow($storyId, $fields);
    if ($db_result) {
        $memcached_client->add($cache_key, $db_result, self::CACHE_EXPIRE);
    }
    return $db_result;
  }

  public function updateStoryRow ($storyId, $info) {
    $memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($storyId);
    $memcached_client->delete($cache_key);
    $db_result = parent::updateStoryRow($storyId, $info);
    return $db_result;
  }

  public function deleteStoryRow ($storyId) {
    $memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($storyId);
    $memcached_client->delete($cache_key);
    $db_result = parent::deleteStoryRow($storyId);
    return $db_result;
  }
}
