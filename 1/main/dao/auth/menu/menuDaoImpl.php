<?php

class menuDaoImpl {

  const DB_NAME = SAE_MYSQL_DB;

	const TABLE_MENU = 'admin_menu';

	protected static $table_fields_ = array('menu_id', 'menu_name', 'tid', 'create_time', 'update_time', 'operator_name');

	public function getMenuRow ($menu_id, $fields) {
		if (is_array($fields) && $fields) {
      if (!in_array("menu_id", $fields)) {
        $fields[] = "menu_id";
      }
    } else {
      $fields = array_diff(self::$table_fields_, array('create_time', 'update_time', 'operator_name'));
    }

    $condition = "where menu_id=$menu_id limit 1";

    $result = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_MENU, $fields, $condition);

    return $result;
	}

	public function getMenuList ($params, $fields) {
		$result = array();

		$default = array('offset' => 0, 'limit' => 30, 'order' => 'menu_id ASC');
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
      if (!in_array("menu_id", $fields)) {
        $fields[] = "menu_id";
      }
    } else {
      $fields = array_diff(self::$table_fields_, array('create_time', 'update_time', 'operator_name'));
    }

    $result['list'] = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_MENU, $fields, $condition);
    $result['total'] = MysqlClient::QueryCount(self::DB_NAME, self::TABLE_MENU, $countStr);

    return $result;
	}

	public function createMenuRow ($info) {
		$fields = array_keys($info);

		$rows = array($info);

		$result = MysqlClient::InsertData(self::DB_NAME, self::TABLE_MENU, $fields, $rows);

    return $result ? MysqlClient::GetInsertID(self::DB_NAME) : 0;
	}

	public function updateMenuRow ($menu_id, $info) {
		$field_values = $info;

		$condition = "where menu_id=$menu_id limit 1";

		$result = MysqlClient::UpdateFields(self::DB_NAME, self::TABLE_MENU, $field_values, $condition);

    return $result;
	}

	public function deleteMenuRow ($menu_id) {
		$condition = "where menu_id=$menu_id limit 1";

		$result = MysqlClient::Delete(self::DB_NAME, self::TABLE_MENU, $condition);
    
    return $result;
	}
}
