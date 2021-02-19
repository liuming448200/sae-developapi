<?php

class actionDaoImpl {

  const DB_NAME = SAE_MYSQL_DB;

	const TABLE_ACTION = 'admin_action';

	protected static $table_fields_ = array('action_id', 'action_name', 'menu_id', 'action', 'create_time', 'update_time', 'operator_name');

	public function getActionRow ($action_id, $fields) {
		if (is_array($fields) && $fields) {
      if (!in_array("action_id", $fields)) {
        $fields[] = "action_id";
      }
    } else {
      $fields = array_diff(self::$table_fields_, array('create_time', 'update_time', 'operator_name'));
    }

    $condition = "where action_id=$action_id limit 1";

    $result = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_ACTION, $fields, $condition);

    return $result;
	}

	public function getActionList ($params, $fields) {
		$result = array();

		$default = array('offset' => 0, 'limit' => 30, 'order' => 'action_id ASC');
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
      if (!in_array("action_id", $fields)) {
        $fields[] = "action_id";
      }
    } else {
      $fields = array_diff(self::$table_fields_, array('create_time', 'update_time', 'operator_name'));
    }

    $result['list'] = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_ACTION, $fields, $condition);
    $result['total'] = MysqlClient::QueryCount(self::DB_NAME, self::TABLE_ACTION, $countStr);

    return $result;
	}

	public function createAcitonRow ($info) {
		$fields = array_keys($info);

		$rows = array($info);

		$result = MysqlClient::InsertData(self::DB_NAME, self::TABLE_ACTION, $fields, $rows);

    return $result ? MysqlClient::GetInsertID(self::DB_NAME) : 0;
	}

	public function updateActionRow ($action_id, $info) {
		$field_values = $info;

		$condition = "where action_id=$action_id limit 1";

		$result = MysqlClient::UpdateFields(self::DB_NAME, self::TABLE_ACTION, $field_values, $condition);

    return $result;
	}

  public function deleteActionRow ($action_id) {
    $condition = "where action_id=$action_id limit 1";

    $result = MysqlClient::Delete(self::DB_NAME, self::TABLE_ACTION, $condition);
    
    return $result;
  }
}
