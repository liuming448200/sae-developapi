<?php
require('text2audioImpl.php');

class cachedText2audioImpl extends text2audioImpl {
	const MEMCACHE_GROUP = 'default';
  const CACHE_EXPIRE = 2592000;

  const CACHE_BAIDU_TTS_ACCESS_TOKEN = 'baidu-tts-access-token';

  private function getCacheKey () {
    return self::CACHE_BAIDU_TTS_ACCESS_TOKEN;
  }

  public function checkAccessToken () {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey();
    $cached_result = $memcached_client->get($cache_key);
    if ($cached_result) {
      return $cached_result;
    }
    $db_result = parent::checkAccessToken();
    if ($db_result) {
      $memcached_client->add($cache_key, $db_result, self::CACHE_EXPIRE);
    }
    return $db_result;
  }

  public function updateAccessToken ($refresh_token, $info) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey();
    $memcached_client->delete($cache_key);
    $db_result = parent::updateAccessToken($refresh_token, $info);
    return $db_result;
  }
}
