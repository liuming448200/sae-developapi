<?php
require('usergroupDaoImpl.php');

class cachedUsergroupDaoImpl extends usergroupDaoImpl {
	const MEMCACHE_GROUP = 'default';
	const CACHE_EXPIRE = 86400;

	const CACHE_USER_GROUP_PREFIX = 'user-group-';

	private function getCacheKey ($group_id) {
    return self::CACHE_USER_GROUP_PREFIX . $group_id;
  }

  public function getUsergroupRow ($group_id, $fields) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($group_id);
    $cached_result = $memcached_client->get($cache_key);
    if ($cached_result) {
        return $cached_result;
    }
    $db_result = parent::getUsergroupRow($group_id, $fields);
    if ($db_result) {
        $memcached_client->add($cache_key, $db_result, self::CACHE_EXPIRE);
    }
    return $db_result;
  }

  public function updateUsergroupRow ($group_id, $info) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($group_id);
    $memcached_client->delete($cache_key);
    $db_result = parent::updateUsergroupRow($group_id, $info);
    return $db_result;
  }

  public function deleteUsergroupRow ($group_id) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($group_id);
    $memcached_client->delete($cache_key);
    $db_result = parent::deleteUsergroupRow($group_id);
    return $db_result;
  }
}
