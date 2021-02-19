<?php
require(PHP_ROOT . 'libs/util/MemCachedClient.php');
require('appDaoImpl.php');

class cachedAppDaoImpl extends appDaoImpl {
	const MEMCACHE_GROUP = 'default';
	const CACHE_EXPIRE = 2592000;

    const CACHE_APP_INFO_PREFIX = 'app-info-';

    private function getCacheKey ($app_key) {
        return self::CACHE_APP_INFO_PREFIX . $app_key;
    }

	public function getAppRow ($app_key) {
        $memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
        $cache_key = self::getCacheKey($app_key);
        $cached_result = $memcached_client->get($cache_key);
        if ($cached_result) {
            return $cached_result;
        }
        $db_result = parent::getAppRow($app_key);
        if ($db_result) {
            $memcached_client->add($cache_key, $db_result, self::CACHE_EXPIRE);
        }
        return $db_result;
	}
}
