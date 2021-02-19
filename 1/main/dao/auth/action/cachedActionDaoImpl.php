<?php
require('actionDaoImpl.php');

class cachedActionDaoImpl extends actionDaoImpl {
	const MEMCACHE_GROUP = 'default';
	const CACHE_EXPIRE = 86400;

	const CACHE_ACTION_PREFIX = 'action-';

	private function getCacheKey ($action_id) {
    return self::CACHE_ACTION_PREFIX . $action_id;
  }

  public function getActionRow ($action_id, $fields) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($action_id);
    $cached_result = $memcached_client->get($cache_key);
    if ($cached_result) {
        return $cached_result;
    }
    $db_result = parent::getActionRow($action_id, $fields);
    if ($db_result) {
        $memcached_client->add($cache_key, $db_result, self::CACHE_EXPIRE);
    }
    return $db_result;
  }

  public function updateActionRow ($action_id, $info) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($action_id);
    $memcached_client->delete($cache_key);
    $db_result = parent::updateActionRow($action_id, $info);
    return $db_result;
  }

  public function deleteActionRow ($action_id) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($action_id);
    $memcached_client->delete($cache_key);
    $db_result = parent::deleteActionRow($action_id);
    return $db_result;
  }
}
