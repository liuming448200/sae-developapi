<?php

class storyDaoImpl {

  const DB_NAME = SAE_MYSQL_DB;

  const TABLE_STORY_BASE_INFO = 'story_base_info';

  protected static $table_fields_ = array('story_id', 'name', 'content', 'implication', 'pic', 'tid', 'create_time', 'update_time');
  
  public function getStoryRow ($storyId, $fields) {
    if (is_array($fields) && $fields) {
      if (!in_array("story_id", $fields)) {
        $fields[] = "story_id";
      }
    } else {
      $fields = array_diff(self::$table_fields_, array('tid', 'create_time', 'update_time'));
    }

    $condition = "where story_id=$storyId limit 1";

    $result = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_STORY_BASE_INFO, $fields, $condition);

    return $result;
  }

  public function getStoryList ($params, $fields) {
    $result = array();

    $default = array('offset' => 0, 'limit' => 30, 'order' => 'convert(name using gbk)');
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
      } elseif (in_array(substr( trim($v) , 0, 1 ), array(">","<","!"))){
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
      if (!in_array("story_id", $fields)) {
        $fields[] = "story_id";
      }
    } else {
      $fields = array_diff(self::$table_fields_, array('tid', 'create_time', 'update_time'));
    }

    $result['list'] = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_STORY_BASE_INFO, $fields, $condition);
    $result['total'] = MysqlClient::QueryCount(self::DB_NAME, self::TABLE_STORY_BASE_INFO, $countStr);

    return $result;
  }

  public function createStoryRow ($info) {
    $fields = array_keys($info);

    $rows = array($info);

    $result = MysqlClient::InsertData(self::DB_NAME, self::TABLE_STORY_BASE_INFO, $fields, $rows);

    return $result ? MysqlClient::GetInsertID(self::DB_NAME) : 0;
  }

  public function updateStoryRow ($storyId, $info) {
    $field_values = $info;

    $condition = "where story_id=$storyId limit 1";
    
    $result = MysqlClient::UpdateFields(self::DB_NAME, self::TABLE_STORY_BASE_INFO, $field_values, $condition);

    return $result;
  }

  public function deleteStoryRow ($storyId) {
    $condition = "where story_id=$storyId limit 1";
    
    $result = MysqlClient::Delete(self::DB_NAME, self::TABLE_STORY_BASE_INFO, $condition);
    
    return $result;
  }
}
