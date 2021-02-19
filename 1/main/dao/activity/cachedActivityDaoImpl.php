<?php
require('activityDaoImpl.php');

class cachedActivityDaoImpl extends activityDao {
	const MEMCACHE_GROUP = 'default';
	const CACHE_EXPIRE = 86400;

	const CACHE_ACTIVITY_INFO_PREFIX = 'activity-info-';

	private function getCacheKey ($activityId) {
    return self::CACHE_ACTIVITY_INFO_PREFIX . $activityId;
  }

  public function getActivityRow ($activityId, $fields) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
  	$cache_key = self::getCacheKey($activityId);
  	$cached_result = $memcached_client->get($cache_key);
  	if ($cached_result) {
      return $cached_result;
    }
    $db_result = parent::getActivityRow($activityId, $fields);
    if ($db_result) {
      $memcached_client->add($cache_key, $db_result, self::CACHE_EXPIRE);
    }
    return $db_result;
  }

  public function updateActivityRow ($activityId, $info) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($activityId);
    $memcached_client->delete($cache_key);
    $db_result = parent::updateActivityRow($activityId, $info);
    return $db_result;
  }

  public function deleteActivityRow ($activityId) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($activityId);
    $memcached_client->delete($cache_key);
    $db_result = parent::deleteActivityRow($activityId);
    return $db_result;
  }
}
