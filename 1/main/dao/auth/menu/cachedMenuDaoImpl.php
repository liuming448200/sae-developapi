<?php
require('menuDaoImpl.php');

class cachedMenuDaoImpl extends menuDaoImpl {
	const MEMCACHE_GROUP = 'default';
	const CACHE_EXPIRE = 86400;

	const CACHE_MENU_PREFIX = 'menu-';

	private function getCacheKey ($menu_id) {
    return self::CACHE_MENU_PREFIX . $menu_id;
  }

  public function getMenuRow ($menu_id, $fields) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($menu_id);
    $cached_result = $memcached_client->get($cache_key);
    if ($cached_result) {
        return $cached_result;
    }
    $db_result = parent::getMenuRow($menu_id, $fields);
    if ($db_result) {
        $memcached_client->add($cache_key, $db_result, self::CACHE_EXPIRE);
    }
    return $db_result;
  }

  public function updateMenuRow ($menu_id, $info) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($menu_id);
    $memcached_client->delete($cache_key);
    $db_result = parent::updateMenuRow($menu_id, $info);
    return $db_result;
  }

  public function deleteMenuRow ($menu_id) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($menu_id);
    $memcached_client->delete($cache_key);
    $db_result = parent::deleteMenuRow($menu_id);
    return $db_result;
  }
}
