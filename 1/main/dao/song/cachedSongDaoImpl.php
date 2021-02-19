<?php
require('songDaoImpl.php');

class cachedSongDaoImpl extends songDaoImpl {
  const MEMCACHE_GROUP = 'default';
  const CACHE_EXPIRE = 86400;

  const CACHE_SONG_INFO_PREFIX = 'song-info-';

  private function getCacheKey ($language, $songId) {
    return self::CACHE_SONG_INFO_PREFIX . $language . $songId;
  }

  public function getSongRow ($language, $songId, $fields) {
  	$memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($language, $songId);
    $cached_result = $memcached_client->get($cache_key);
    if ($cached_result) {
        return $cached_result;
    }
    $db_result = parent::getSongRow($language, $songId, $fields);
    if ($db_result) {
        $memcached_client->add($cache_key, $db_result, self::CACHE_EXPIRE);
    }
    return $db_result;
  }

  public function updateSongRow ($language, $songId, $info) {
    $memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($language, $songId);
    $memcached_client->delete($cache_key);
    $db_result = parent::updateSongRow($language, $songId, $info);
    return $db_result;
  }

  public function deleteSongRow ($language, $songId) {
    $memcached_client = MemCachedClient::GetInstance(self::MEMCACHE_GROUP);
    $cache_key = self::getCacheKey($language, $songId);
    $memcached_client->delete($cache_key);
    $db_result = parent::deleteSongRow($language, $songId);
    return $db_result;
  }
}
