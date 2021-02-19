<?php

class contentDaoImpl {

  const DB_NAME = SAE_MYSQL_DB;

	const TABLE_CONTENT_TYPE = 'content_type';

	protected static $table_fields_ = array('tid', 'typename', 'create_time', 'update_time');

	public function getContentRow ($tid, $fields) {
		if (is_array($fields) && $fields) {
      if (!in_array("tid", $fields)) {
        $fields[] = "tid";
      }
    } else {
      $fields = array_diff(self::$table_fields_, array('create_time', 'update_time'));
    }

    $condition = "where tid=$tid limit 1";

    $result = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_CONTENT_TYPE, $fields, $condition);

    return $result;
	}

	public function getContentList ($params, $fields) {
		$result = array();

		$default = array('offset' => 0, 'limit' => 30, 'order' => 'tid ASC');
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
      if (!in_array("tid", $fields)) {
        $fields[] = "tid";
      }
    } else {
      $fields = array_diff(self::$table_fields_, array('create_time', 'update_time'));
    }

    $result['list'] = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_CONTENT_TYPE, $fields, $condition);
    $result['total'] = MysqlClient::QueryCount(self::DB_NAME, self::TABLE_CONTENT_TYPE, $countStr);

    return $result;
	}

	public function createContentRow ($info) {
		$fields = array_keys($info);

		$rows = array($info);

		$result = MysqlClient::InsertData(self::DB_NAME, self::TABLE_CONTENT_TYPE, $fields, $rows);

    return $result ? MysqlClient::GetInsertID(self::DB_NAME) : 0;
	}

	public function updateContentRow ($tid, $info) {
		$field_values = $info;

		$condition = "where tid=$tid limit 1";

		$result = MysqlClient::UpdateFields(self::DB_NAME, self::TABLE_CONTENT_TYPE, $field_values, $condition);

    return $result;
	}

	public function deleteContentRow ($tid) {
		$condition = "where tid=$tid limit 1";

		$result = MysqlClient::Delete(self::DB_NAME, self::TABLE_CONTENT_TYPE, $condition);
    
    return $result;
	}
}
