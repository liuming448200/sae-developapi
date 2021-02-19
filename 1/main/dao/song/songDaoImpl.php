<?php

class songDaoImpl {

  const DB_NAME = SAE_MYSQL_DB;

  const TABLE_SONG_BASE_INFO_CN = 'song_base_info_cn'; //中文儿歌基本信息表
  const TABLE_SONG_BASE_INFO_EN = 'song_base_info_en'; //英文儿歌基本信息表

  protected static $table_fields_ = array('song_id', 'name', 'content', 'song', 'pic', 'tid', 'create_time', 'update_time');

  public function getSongRow ($language, $songId, $fields) {
    if (is_array($fields) && $fields) {
      if (!in_array("song_id", $fields)) {
        $fields[] = "song_id";
      }
    } else {
      $fields = array_diff(self::$table_fields_, array('tid', 'create_time', 'update_time'));
    }
    
    $condition = "where song_id='$songId' limit 1";

    if ('chinese' == $language) {
      $result = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_SONG_BASE_INFO_CN, $fields, $condition);
    } elseif ('english' == $language) {
      $result = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_SONG_BASE_INFO_EN, $fields, $condition);
    }

    return $result;
  }

  public function getSongList ($language, $params, $fields) {
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
        $db[] = "$k in({$vStr}) ";
      } elseif (in_array(substr(trim($v) , 0, 1), array(">","<","!"))) {
        $db[] = "$k {$v}";
      } else {
        $db[] = "$k = {$v}";
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
      if (!in_array("song_id", $fields)) {
        $fields[] = "song_id";
      }
    } else {
      $fields = array_diff(self::$table_fields_, array('tid', 'create_time', 'update_time'));
    }

    if ('chinese' == $language) {
      $result['list'] = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_SONG_BASE_INFO_CN, $fields, $condition);
      $result['total'] = MysqlClient::QueryCount(self::DB_NAME, self::TABLE_SONG_BASE_INFO_CN, $countStr);
    } elseif ('english' == $language) {
      $result['list'] = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_SONG_BASE_INFO_EN, $fields, $condition);
      $result['total'] = MysqlClient::QueryCount(self::DB_NAME, self::TABLE_SONG_BASE_INFO_EN, $countStr);
    }

    return $result;
  }

  public function createSongRow ($language, $info) {
    $fields = array_keys($info);

    $rows = array($info);

    if ('chinese' == $language) {
      $result = MysqlClient::InsertData(self::DB_NAME, self::TABLE_SONG_BASE_INFO_CN, $fields, $rows);
    } elseif ('english' == $language) {
      $result = MysqlClient::InsertData(self::DB_NAME, self::TABLE_SONG_BASE_INFO_EN, $fields, $rows);
    }

    return $result ? MysqlClient::GetInsertID(self::DB_NAME) : 0;
  }

  public function updateSongRow ($language, $songId, $info) {
    $field_values = $info;

    $condition = "where song_id='$songId' limit 1";

    if ('chinese' == $language) {
      $result = MysqlClient::UpdateFields(self::DB_NAME, self::TABLE_SONG_BASE_INFO_CN, $field_values, $condition);
    } elseif ('english' == $language) {
      $result = MysqlClient::UpdateFields(self::DB_NAME, self::TABLE_SONG_BASE_INFO_EN, $field_values, $condition);
    }

    return $result;
  }

  public function deleteSongRow ($language, $songId) {
    $condition = "where song_id='$songId' limit 1";

    if ('chinese' == $language) {
      $result = MysqlClient::Delete(self::DB_NAME, self::TABLE_SONG_BASE_INFO_CN, $condition);
    } elseif ('english' == $language) {
      $result = MysqlClient::Delete(self::DB_NAME, self::TABLE_SONG_BASE_INFO_EN, $condition);
    }
    
    return $result;
  }
}
