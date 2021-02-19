<?php
require('childAccountDaoImpl.php');

class cachedChildAccountDaoImpl extends childAccountDaoImpl {
  const MEMCACHE_GROUP = 'default';
  const CACHE_EXPIRE = 86400;

  const CACHE_CHILD_ACCOUNT_PREFIX = 'child-account-';

  private function getCacheKey ($keyword) {
    return self::CACHE_CHILD_ACCOUNT_PREFIX . $keyword;
  }

  public function getChildInfoRow ($uid, $fields = 1, $cid = 0) {
    $db_result = parent::getChildInfoRow($uid, $fields, $cid);
    return $db_result;
  }

  public function updateChildInfoRow ($uid, $cid, $info) {
    $db_result = parent::updateChildInfoRow($uid, $cid, $info);
    return $db_result;
  }

  public function deleteChildInfoRow ($uid, $cid) {
    $db_result = parent::deleteChildInfoRow($uid, $cid);
    return $db_result;
  }
}
