<?php

class wordDaoImpl {

  const DB_NAME = SAE_MYSQL_DB;

	const TABLE_WORD_BASE_INFO = 'word_base_info'; //单词基本信息表

  protected static $table_fields_ = array('word_id','category_id','english','chinese','example_en','example_cn','pic','tid','create_time','update_time');

	public function getWordRow ($wordId, $fields) {
    if (is_array($fields) && $fields) {
      if (!in_array("word_id", $fields)) {
        $fields[] = "word_id";
      }
    } else {
      $fields = array_diff(self::$table_fields_, array('tid', 'create_time', 'update_time'));
    }

    $condition = "where word_id=$wordId limit 1";

    $result = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_WORD_BASE_INFO, $fields, $condition);

    return $result;
	}

	public function getWordList ($params, $fields) {
    $result = array();

    $default = array('offset' => 0, 'limit' => 30, 'order' => 'english');
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
      if (!in_array("word_id", $fields)) {
        $fields[] = "word_id";
      }
    } else {
      $fields = array_diff(self::$table_fields_, array('tid', 'create_time', 'update_time'));
    }

    $result['list'] = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_WORD_BASE_INFO, $fields, $condition);
    $result['total'] = MysqlClient::QueryCount(self::DB_NAME, self::TABLE_WORD_BASE_INFO, $countStr);

    return $result;
	}

	public function createWordRow ($info) {
		$fields = array_keys($info);

    $rows = array($info);

    $result = MysqlClient::InsertData(self::DB_NAME, self::TABLE_WORD_BASE_INFO, $fields, $rows);

    return $result ? MysqlClient::GetInsertID(self::DB_NAME) : 0;
	}

	public function updateWordRow ($wordId, $info) {
		$field_values = $info;

    $condition = "where word_id=$wordId limit 1";

    $result = MysqlClient::UpdateFields(self::DB_NAME, self::TABLE_WORD_BASE_INFO, $field_values, $condition);

    return $result;
	}

	public function deleteWordRow ($wordId) {
		$condition = "where word_id=$wordId limit 1";

    $result = MysqlClient::Delete(self::DB_NAME, self::TABLE_WORD_BASE_INFO, $condition);

    return $result;
	}

  public function deleteWordsByCategory ($categoryId) {
    $condition = "where category_id=$categoryId";

    $result = MysqlClient::Delete(self::DB_NAME, self::TABLE_WORD_BASE_INFO, $condition);

    return $result;
  }
}
