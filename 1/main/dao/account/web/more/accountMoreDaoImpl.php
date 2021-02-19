<?php

class accountMoreDaoImpl {

  const DB_NAME = SAE_MYSQL_DB;

  const TABLE_USER_INFO_MORE = 'web_user_more';

  protected static $table_fields_ = array('id','uid','nickname','head_portrait','realname','gender','birthday','address','create_time','update_time');

  public function getUserInfoMoreRow ($uid, $fields) {
    if (is_array($fields) && $fields) {
      if (!in_array("uid", $fields)) {
        $fields[] = "uid";
      }
    } else {
      $fields = array_diff(self::$table_fields_, array('id', 'uid', 'create_time', 'update_time'));
    }

    $condition = "where uid='$uid' limit 1";

    $result = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_USER_INFO_MORE, $fields, $condition);

    return $result;
  }

  public function getUserInfoMoreList ($params, $fields) {
    $result = array();

    $default = array('offset' => 0, 'limit' => 10, 'order' => 'uid ASC');
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
      if (!in_array("uid", $fields)) {
        $fields[] = "uid";
      }
    } else {
      $fields = array_diff(self::$table_fields_, array('id', 'create_time', 'update_time'));
    }

    $result['list'] = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_USER_INFO_MORE, $fields, $condition);
    $result['total'] = MysqlClient::QueryCount(self::DB_NAME, self::TABLE_USER_INFO_MORE, $countStr);

    return $result;
  }

  public function createUserInfoMoreRow ($info) {
    $fields = array_keys($info);

    $rows = array($info);

    $result = MysqlClient::InsertData(self::DB_NAME, self::TABLE_USER_INFO_MORE, $fields, $rows);

    return $result ? MysqlClient::GetInsertID(self::DB_NAME) : 0;
  }

  public function updateUserInfoMoreRow ($uid, $info) {
    $field_values = $info;

    $condition = "where uid='$uid' limit 1";
    
    $result = MysqlClient::UpdateFields(self::DB_NAME, self::TABLE_USER_INFO_MORE, $field_values, $condition);

    return $result;
  }

  public function deleteUserInfoMoreRow ($uid) {
    $condition = "where uid='$uid' limit 1";
    
    $result = MysqlClient::Delete(self::DB_NAME, self::TABLE_USER_INFO_MORE, $condition);
    
    return $result;
  }
}
