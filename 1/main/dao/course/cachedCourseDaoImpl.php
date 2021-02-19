<?php
require('courseDaoImpl.php');

class cachedCourseDaoImpl extends courseDao {
	const MEMCACHE_GROUP = 'default';
	const CACHE_EXPIRE = 86400;

	const CACHE_COURSE_INFO_PREFIX = 'course-info-';

	private function getCacheKey ($courseId) {
    return self::CACHE_COURSE_INFO_PREFIX . $courseId;
  }

  public function getCourseRow ($courseId, $fields) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
  	$cache_key = self::getCacheKey($courseId);
  	$cached_result = $memcached_client->get($cache_key);
  	if ($cached_result) {
      return $cached_result;
    }
    $db_result = parent::getCourseRow($courseId, $fields);
    if ($db_result) {
      $memcached_client->add($cache_key, $db_result, self::CACHE_EXPIRE);
    }
    return $db_result;
  }

  public function updateCourseRow ($courseId, $info) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($courseId);
    $memcached_client->delete($cache_key);
    $db_result = parent::updateCourseRow($courseId, $info);
    return $db_result;
  }

  public function deleteCourseRow ($courseId) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($courseId);
    $memcached_client->delete($cache_key);
    $db_result = parent::deleteCourseRow($courseId);
    return $db_result;
  }
}
