<?php

class accountDaoImpl {

	const DB_NAME = SAE_MYSQL_DB;

	const TABLE_USER_INFO = 'web_user_info';
	const TABLE_ACCESS_TOKEN = 'web_access_token';

	protected static $table_fields_ = array('id', 'uid', 'mobile', 'password', 'username', 'create_time', 'update_time');

	const KEYWORD_TYPE_MERMBER_ID = 0; // 会员ID
	const KEYWORD_TYPE_MOBILE     = 1; // 手机号

	public function register ($info) {
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
		$condition = " where access_token='$accessToken' and uid='$uid' limit 1";

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
}
