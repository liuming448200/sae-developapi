<?php

class wordCategoryDaoImpl {

  const DB_NAME = SAE_MYSQL_DB;

	const TABLE_WORD_CATEGORY = 'word_category'; //单词分类表

  protected static $table_fields_ = array('category_id', 'english', 'chinese', 'tid', 'create_time', 'update_time');

	public function getWordCategoryRow ($categoryId, $fields) {
    if (is_array($fields) && $fields) {
      if (!in_array("category_id", $fields)) {
        $fields[] = "category_id";
      }
    } else {
      $fields = array_diff(self::$table_fields_, array('tid', 'create_time', 'update_time'));
    }

    $condition = "where category_id=$categoryId limit 1";

    $result = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_WORD_CATEGORY, $fields, $condition);

    return $result;
	}

	public function getWordCategoryList ($params, $fields) {
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
      if (!in_array("category_id", $fields)) {
        $fields[] = "category_id";
      }
    } else {
      $fields = array_diff(self::$table_fields_, array('tid', 'create_time', 'update_time'));
    }

    $result['list'] = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_WORD_CATEGORY, $fields, $condition);
    $result['total'] = MysqlClient::QueryCount(self::DB_NAME, self::TABLE_WORD_CATEGORY, $countStr);

    return $result;
	}

	public function createWordCategoryRow ($info) {
		$fields = array_keys($info);

    $rows = array($info);

    $result = MysqlClient::InsertData(self::DB_NAME, self::TABLE_WORD_CATEGORY, $fields, $rows);

    return $result ? MysqlClient::GetInsertID(self::DB_NAME) : 0;
	}

	public function updateWordCategoryRow ($categoryId, $info) {
		$field_values = $info;

    $condition = "where category_id=$categoryId limit 1";

    $result = MysqlClient::UpdateFields(self::DB_NAME, self::TABLE_WORD_CATEGORY, $field_values, $condition);

    return $result;
	}

	public function deleteWordCategoryRow ($categoryId) {
		$condition = "where category_id=$categoryId limit 1";

    $result = MysqlClient::Delete(self::DB_NAME, self::TABLE_WORD_CATEGORY, $condition);

    return $result;
	}
}
