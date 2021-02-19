<?php
require('accountDaoImpl.php');

class cachedAccountDaoImpl extends accountDaoImpl {
	const MEMCACHE_GROUP = 'default';
	const CACHE_EXPIRE = 86400;

	const CACHE_ADMIN_ACCOUNT_PREFIX = 'admin-account-';

	private function getCacheKey ($keyword) {
		return self::CACHE_ADMIN_ACCOUNT_PREFIX . $keyword;
	}

	public function getUserInfo ($keyword, $keywordType = self::KEYWORD_TYPE_MERMBER_ID) {
		$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
		$cache_key = self::getCacheKey($keyword);
		$cached_result = $memcached_client->get($cache_key);
		if ($cached_result) {
			return $cached_result;
		}
		$db_result = parent::getUserInfo($keyword, $keywordType);
		if ($db_result) {
			$memcached_client->add($cache_key, $db_result, self::CACHE_EXPIRE);
		}
		return $db_result;
	}

	public function changeMobile ($uid, $mobile) {
		$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($uid);
    if ($cache_key) {
    	$memcached_client->delete($cache_key);
    }
    $cache_key1 = self::getCacheKey($mobile);
    if ($cache_key1) {
    	$memcached_client->delete($cache_key1);
    }
    $db_result = parent::changeMobile($uid, $mobile);
    return $db_result;
	}

	public function setUserStatus ($uid, $status) {
		$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
		$cache_key = self::getCacheKey($uid);
		$memcached_client->delete($cache_key);
	  $db_result = parent::setUserStatus($uid, $status);
		return $db_result;
	}
}
