<?php

class accountDaoImpl {

	const DB_NAME = SAE_MYSQL_DB;

	const TABLE_USER_INFO = 'admin_user_info';
	const TABLE_ACCESS_TOKEN = 'admin_access_token';

	const TABLE_GROUP_USER = 'admin_group_user';
	const TABLE_USER_GROUP = 'admin_user_group';
	const TABLE_ACTION_GROUP = ' admin_action_group';
	const TABLE_ACTION = 'admin_action';
	const TABLE_MENU = 'admin_menu';

	const TABLE_CONTENT_TYPE = 'content_type';

	protected static $table_fields_ = array('id', 'uid', 'mobile', 'password', 'username', 'status', 'create_time', 'update_time');

	const KEYWORD_TYPE_MERMBER_ID = 0; // 会员ID
	const KEYWORD_TYPE_MOBILE     = 1; // 手机号

  public function addUser ($info) {
		$fields = array_keys($info);

		$rows = array($info);

		$result = MysqlClient::InsertData(self::DB_NAME, self::TABLE_USER_INFO, $fields, $rows);

		return $result ? MysqlClient::GetInsertID(self::DB_NAME) : 0;
	}

	public function login ($username, $password) {
		$fields = array_diff(self::$table_fields_, array('id', 'password', 'create_time', 'update_time'));

		$condition = "where (mobile='$username' or username='$username') and password='".md5($password)."' limit 1";

		$result = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_USER_INFO, $fields, $condition);
		
		return $result;
	}

	public function insertAccessToken ($accessToken, $uid, $login_time) {
		$row = array(
      'access_token' => $accessToken,
      'uid' => $uid,
      'login_time' => $login_time
    );

    $fields = array_keys($row);

    $rows = array($row);

		$result = MysqlClient::InsertData(self::DB_NAME, self::TABLE_ACCESS_TOKEN, $fields, $rows);
		
		return $result ? MysqlClient::GetInsertID(self::DB_NAME) : 0;
	}

	public function updateAccessToken ($accessToken, $uid, $login_time) {
		$field_values = array(
      'access_token' => $accessToken,
      'login_time' => $login_time
    );

    $condition = "where uid='$uid' limit 1";

    $result = MysqlClient::UpdateFields(self::DB_NAME, self::TABLE_ACCESS_TOKEN, $field_values, $condition);

    return $result;
	}

	public function removeAccessToken ($accessToken, $uid) {
    $condition = "where access_token='$accessToken' and uid='$uid' limit 1";

    $result = MysqlClient::Delete(self::DB_NAME, self::TABLE_ACCESS_TOKEN, $condition);

    return $result;
	}

	public function checkAccessToken ($accessToken) {
		$fields = array('access_token', 'uid', 'login_time');

		$condition = "where access_token='$accessToken' limit 1";

		$result = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_ACCESS_TOKEN, $fields, $condition);
		
		return $result;
	}

	public function checkAccessTokenByUid ($uid) {
		$fields = array('access_token', 'uid', 'login_time');

		$condition = "where uid='$uid' limit 1";

		$result = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_ACCESS_TOKEN, $fields, $condition);
		
		return $result;
	}

	public function getUserInfo ($keyword, $keywordType = self::KEYWORD_TYPE_MERMBER_ID) {
		$fields = array_diff(self::$table_fields_, array('id', 'password', 'create_time', 'update_time'));

		if (self::KEYWORD_TYPE_MERMBER_ID == $keywordType) {
			$condition = "where uid='$keyword' limit 1";
		} else if (self::KEYWORD_TYPE_MOBILE == $keywordType) {
			$condition = "where mobile='$keyword' limit 1";
		}

		$result = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_USER_INFO, $fields, $condition);

		return $result;
	}

	public function resetPassword ($mobile, $password) {
		$fields = array_diff(self::$table_fields_, array('id', 'password', 'create_time', 'update_time'));

		$condition = "where mobile='$mobile' and password='".md5($password)."' limit 1";

		$result = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_USER_INFO, $fields, $condition);
		if (!$result) {
			$field_values = array(
				'password' => md5($password)
			);

			$condition = "where mobile='$mobile' limit 1";

			$result = MysqlClient::UpdateFields(self::DB_NAME, self::TABLE_USER_INFO, $field_values, $condition);
		} else {
			return 1;
		}

		return $result;
	}

	public function changePassword ($uid, $oldpassword, $newpassword) {
		$oldpassword = md5($oldpassword);
    $newpassword = md5($newpassword);

    $fields = array_diff(self::$table_fields_, array('id', 'password', 'create_time', 'update_time'));

    $condition = "where uid='$uid' and password='".$oldpassword."' limit 1";

    $result = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_USER_INFO, $fields, $condition);
    if ($result) {
    	$field_values = array(
				'password' => $newpassword
			);

    	$condition = "where uid='$uid' limit 1";

			$result = MysqlClient::UpdateFields(self::DB_NAME, self::TABLE_USER_INFO, $field_values, $condition);
    } else {
    	return 1;
    }

    return $result;
	}

	public function changeMobile ($uid, $mobile) {
		$field_values = array(
			'mobile' => $mobile
		);

		$condition = "where uid='$uid' limit 1";

		$result = MysqlClient::UpdateFields(self::DB_NAME, self::TABLE_USER_INFO, $field_values, $condition);

		return $result;
	}

	public function checkMobileExist ($mobile) {
		$condition = "where mobile='$mobile'";

		$count = MysqlClient::QueryCount(self::DB_NAME, self::TABLE_USER_INFO, $condition);

		return $count > 0 ? true : false;
	}

	public function checkUserExist ($username) {
		$condition = "where username='$username'";

		$count = MysqlClient::QueryCount(self::DB_NAME, self::TABLE_USER_INFO, $condition);

		return $count > 0 ? true : false;
	}

	public function getUserList ($params, $fields) {
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
      $fields = array_diff(self::$table_fields_, array('id', 'password', 'create_time', 'update_time'));
    }

    $result['list'] = MysqlClient::QueryFields(self::DB_NAME, self::TABLE_USER_INFO, $fields, $condition);
    $result['total'] = MysqlClient::QueryCount(self::DB_NAME, self::TABLE_USER_INFO, $countStr);

    return $result;
	}

	public function setUserStatus ($uid, $status) {
		$field_values = array(
			'status' => $status
		);

		$condition = "where uid='$uid' limit 1";

		$result = MysqlClient::UpdateFields(self::DB_NAME, self::TABLE_USER_INFO, $field_values, $condition);

		return $result;
	}

	public function addGroupToUserRelation ($uid, $usergroups) {
		$fields = array('group_id', 'uid');

		foreach ($usergroups as $usergroup) {
			$row['group_id'] = $usergroup;
			$row['uid'] = $uid;
			$rows[] = $row;
		}

		$result = MysqlClient::InsertData(self::DB_NAME, self::TABLE_GROUP_USER, $fields, $rows);

		return $result ? MysqlClient::GetInsertID(self::DB_NAME) : 0;
	}

	public function getGroupToUserRelation ($uid) {
		$where = " where uid='$uid'";
		$sql = 'select b.group_id, c.group_name from (';
		$sql .= 'select group_id from ' . self::TABLE_GROUP_USER . $where;
		$sql .= ') b ';
		$sql .= 'join ' . self::TABLE_USER_GROUP . ' c ';
		$sql .= 'on b.group_id = c.group_id';

		$result = MysqlClient::ExecuteQuery(self::DB_NAME, $sql);

    return $result;
	}

	public function deleteGroupToUserRelation ($uid) {
		$condition = "where uid='$uid'";

		$result = MysqlClient::Delete(self::DB_NAME, self::TABLE_GROUP_USER, $condition);
    
    return $result;
	}

	public function getUserMenu ($usergroups) {
		$where = ' where group_id in (' . $usergroups . ')';
		$sql = 'select a.action, b.menu_id, c.menu_name, c.tid, d.typename from (';
		$sql .= 'select action from ' . self::TABLE_ACTION_GROUP . $where;
		$sql .= ') a ';
		$sql .= 'join ' . self::TABLE_ACTION . ' b ';
		$sql .= 'on a.action = b.action ';
		$sql .= 'join ' . self::TABLE_MENU . ' c ';
		$sql .= 'on b.menu_id = c.menu_id ';
		$sql .= 'join ' . self::TABLE_CONTENT_TYPE . ' d ';
		$sql .= 'on c.tid = d.tid';

		$result = MysqlClient::ExecuteQuery(self::DB_NAME, $sql);

    return $result;
	}
}
