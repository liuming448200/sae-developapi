<?php

class childAccountDaoImpl {

  const DB_NAME = SAE_MYSQL_DB;

  const TABLE_CHILD_INFO = 'web_user_child';

  protected static $table_fields_ = array('cid','uid','nickname','head_portrait','realname','gender','birthday','relationship','current','create_time','update_time');

  public function getChildInfoRow ($uid, $fields, $cid) {
    if (is_array($fields) && $fields) {
      if (!in_array("cid", $fields)) {
        $fields[] = "cid";
      }
    } else {
      $fields = array_diff(self::$table_fields_, array('uid', 'create_time', 'update_time'));
    }

    if ($cid) {
      $condition = "where cid='$cid' limit 1";
    } else {
      $condition = "where uid='$uid' and current=1 limit 1";
    }

    $result = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_CHILD_INFO, $fields, $condition);

    return $result;
  }

  public function getChildInfoList ($params, $fields) {
    $result = array();

    $default = array('offset' => 0, 'limit' => 10, 'order' => 'current DESC, cid ASC');
    $params = array_merge($default, $params);

    $where = $order = $group = $limit = '';

    $db = array();

    foreach ($params as $k => $v) {
      $keys = array_keys($default);
      if (in_array($k, $keys)) {
        continue;
      }
      if ($v && is_array($v)) {
        $vStr = implode(',', $v);
        $db[] = "`$k` in({$vStr}) ";
      } elseif (in_array(substr(trim($v) , 0, 1), array(">","<","!"))){
        $db[] = "`$k` {$v}";
      } else {
        $db[] = "`$k` = {$v}";
      }
    }

    if ($db) {
      $where = 'WHERE '.implode(' AND ', $db);
    }

    if ($params['order']) {
      $order = 'ORDER BY ' . $params['order'];
    }

    if (isset($params['group']) && $params['group']) {
      $group = 'GROUP BY ' . $params['group'];
    }

    if (isset($params['limit'])) {
      $limit = 'LIMIT ' . $params['offset'] . ',' . $params['limit'];
    }

    $condition = "{$where} {$group} {$order}  {$limit}";

    $countStr = "{$where} {$group} {$order}";

    if (is_array($fields) && $fields) {
      if (!in_array("cid", $fields)) {
        $fields[] = "cid";
      }
    } else {
      $fields = array_diff(self::$table_fields_, array('create_time', 'update_time'));
    }

    $result['list'] = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_CHILD_INFO, $fields, $condition);
    $result['total'] = MysqlClient::QueryCount(self::DB_NAME, self::TABLE_CHILD_INFO, $countStr);

    return $result;
  }

  public function createChildInfoRow ($info) {
    $uid = $info['uid'];
    $current = $info['current'];
    if (1 == $current) {
      $condition = "where uid='$uid'";
      $childTotal = MysqlClient::QueryCount(self::DB_NAME, self::TABLE_CHILD_INFO, $condition);
      if ($childTotal > 0) {
        $result = MysqlClient::UpdateFields(self::DB_NAME, self::TABLE_CHILD_INFO, array('current'=>0), $condition);
        if (!$result) {
          return -1;
        }
      }
    }

    $fields = array_keys($info);

    $rows = array($info);

    $result = MysqlClient::InsertData(self::DB_NAME, self::TABLE_CHILD_INFO, $fields, $rows);

    return $result ? MysqlClient::GetInsertID(self::DB_NAME) : 0;
  }

  public function updateChildInfoRow ($uid, $cid, $info) {
    $current = $info['current'];
    if (1 == $current) {
      $condition = "where uid='$uid'";
      $childTotal = MysqlClient::QueryCount(self::DB_NAME, self::TABLE_CHILD_INFO, $condition);
      if ($childTotal > 0) {
        $result = MysqlClient::UpdateFields(self::DB_NAME, self::TABLE_CHILD_INFO, array('current'=>0), $condition);
        if (!$result) {
          return -1;
        }
      }
    }

    $field_values = $info;

    $condition = "where cid='$cid' limit 1";
    
    $result = MysqlClient::UpdateFields(self::DB_NAME, self::TABLE_CHILD_INFO, $field_values, $condition);

    return $result;
  }

  public function deleteChildInfoRow ($uid, $cid) {
    $condition = "where cid='$cid' limit 1";
    
    $result = MysqlClient::Delete(self::DB_NAME, self::TABLE_CHILD_INFO, $condition);
    
    return $result;
  }
}
