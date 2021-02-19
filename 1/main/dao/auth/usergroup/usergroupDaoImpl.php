<?php

class usergroupDaoImpl {

	const DB_NAME = SAE_MYSQL_DB;

	const TABLE_USER_GROUP = 'admin_user_group';
	const TABLE_ACTION_GROUP = ' admin_action_group';
	const TABLE_ACTION = 'admin_action';
	const TABLE_GROUP_USER = 'admin_group_user';
	const TABLE_USER_INFO = 'admin_user_info';

	protected static $table_fields_ = array('group_id', 'group_name', 'group_info', 'create_time', 'update_time', 'operator_name');

	public function getUsergroupRow ($group_id, $fields) {
		if (is_array($fields) && $fields) {
      if (!in_array("group_id", $fields)) {
        $fields[] = "group_id";
      }
    } else {
      $fields = array_diff(self::$table_fields_, array('create_time', 'update_time', 'operator_name'));
    }

    $condition = "where group_id=$group_id limit 1";

    $result = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_USER_GROUP, $fields, $condition);

    return $result;
	}

	public function getActionToGroupRelation ($group_id) {
		$where = " where group_id='$group_id' ";
		$sql = 'select b.action, c.action_name from (';
		$sql .= 'select action from ' . self::TABLE_ACTION_GROUP . $where;
		$sql .= ') b ';
		$sql .= 'join ' . self::TABLE_ACTION . ' c ';
		$sql .= 'on b.action = c.action';

		$result = MysqlClient::ExecuteQuery(self::DB_NAME, $sql);

    return $result;
	}

	public function getGroupToUserRelation ($group_id) {
		$where = " where group_id='$group_id' ";
		$sql = 'select b.uid, c.username from (';
		$sql .= 'select uid from ' . self::TABLE_GROUP_USER . $where;
		$sql .= ') b ';
		$sql .= 'join ' . self::TABLE_USER_INFO . ' c ';
		$sql .= 'on b.uid = c.uid';

		$result = MysqlClient::ExecuteQuery(self::DB_NAME, $sql);

    return $result;
	}

	public function getUsergroupList ($params, $fields) {
		$result = array();

		$default = array('offset' => 0, 'limit' => 30, 'order' => 'group_id ASC');
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
      if (!in_array("group_id", $fields)) {
        $fields[] = "group_id";
      }
    } else {
      $fields = array_diff(self::$table_fields_, array('create_time', 'update_time', 'operator_name'));
    }

    $result['list'] = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_USER_GROUP, $fields, $condition);
    $result['total'] = MysqlClient::QueryCount(self::DB_NAME, self::TABLE_USER_GROUP, $countStr);

    return $result;
	}

	public function createUsergroupRow ($info) {
		$fields = array_keys($info);

		$rows = array($info);

		$result = MysqlClient::InsertData(self::DB_NAME, self::TABLE_USER_GROUP, $fields, $rows);

		return $result ? MysqlClient::GetInsertID(self::DB_NAME) : 0;
	}

	public function createActionToGroupRelation ($group_id, $actions) {
		$fields = array('action', 'group_id');

		foreach ($actions as $action) {
			$row['action'] = $action;
			$row['group_id'] = $group_id;
			$rows[] = $row;
		}

		$result = MysqlClient::InsertData(self::DB_NAME, self::TABLE_ACTION_GROUP, $fields, $rows);

		return $result ? MysqlClient::GetInsertID(self::DB_NAME) : 0;
	}

	public function createGroupToUserRelation ($group_id, $uids) {
		$fields = array('group_id', 'uid');

		foreach ($uids as $uid) {
			$row['uid'] = $uid;
			$row['group_id'] = $group_id;
			$rows[] = $row;
		}

		$result = MysqlClient::InsertData(self::DB_NAME, self::TABLE_GROUP_USER, $fields, $rows);

		return $result ? MysqlClient::GetInsertID(self::DB_NAME) : 0;
	}

	public function updateUsergroupRow ($group_id, $info) {
		$field_values = $info;

		$condition = "where group_id=$group_id limit 1";

		$result = MysqlClient::UpdateFields(self::DB_NAME, self::TABLE_USER_GROUP, $field_values, $condition);

    return $result;
	}

	public function deleteUsergroupRow ($group_id) {
		$condition = "where group_id=$group_id limit 1";

		$result = MysqlClient::Delete(self::DB_NAME, self::TABLE_USER_GROUP, $condition);
    
    return $result;
	}

	public function deleteActionToGroupRelation ($group_id) {
		$condition = "where group_id=$group_id";

		$result = MysqlClient::Delete(self::DB_NAME, self::TABLE_ACTION_GROUP, $condition);
    
    return $result;
	}

	public function deleteGroupToUserRelation ($group_id) {
		$condition = "where group_id=$group_id";

		$result = MysqlClient::Delete(self::DB_NAME, self::TABLE_GROUP_USER, $condition);
    
    return $result;
	}
}
