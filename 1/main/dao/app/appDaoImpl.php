<?php

class appDaoImpl {

  const DB_NAME = SAE_MYSQL_DB;

	const TABLE_APP_MANAGEMENT = 'app_management';

  protected static $table_fields_ = array('id', 'app_key', 'app_secret', 'app_role', 'create_time', 'update_time');

	public function getAppRow ($app_key) {
    $fields = array_diff(self::$table_fields_, array('create_time', 'update_time'));

		$condition = "where app_key='$app_key' limit 1";

    $result = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_APP_MANAGEMENT, $fields, $condition);

    return $result;
	}

	public function getAppList ($params, $fields) {
		$result = array();

		$default = array('offset' => 0, 'limit' => 30, 'order' => 'id ASC');
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
      if (!in_array("id", $fields)) {
        $fields[] = "id";
      }
    } else {
      $fields = array_diff(self::$table_fields_, array('create_time', 'update_time'));
    }

    $result['list'] = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_APP_MANAGEMENT, $fields, $condition);
    $result['total'] = MysqlClient::QueryCount(self::DB_NAME, self::TABLE_APP_MANAGEMENT, $countStr);

    return $result;
	}
}
