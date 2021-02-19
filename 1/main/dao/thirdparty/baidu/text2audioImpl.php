<?php

class text2audioImpl {

	const DB_NAME = SAE_MYSQL_DB;

	const BAIDU_ACCESS_TOKEN = 'baidu_access_token';

	protected static $table_fields_ = array('id','access_token','session_key','scope','refresh_token','session_secret','expires_in','create_time','update_time');

	public function checkAccessToken () {
		$fields = array_diff(self::$table_fields_, array('id', 'update_time'));

		$condition = 'limit 1';

		$result = MysqlClient::QueryFields(self::DB_NAME, self::BAIDU_ACCESS_TOKEN, $fields, $condition);

		return $result;
	}

	public function insertAccessToken ($info) {
		$fields = array_keys($info);

		$rows = array($info);

		$result = MysqlClient::InsertData(self::DB_NAME, self::BAIDU_ACCESS_TOKEN, $fields, $rows);

		return $result ? MysqlClient::GetInsertID(self::DB_NAME) : 0;
	}

	public function updateAccessToken ($refresh_token, $info) {
		$field_values = $info;

		$condition = "where refresh_token='$refresh_token' limit 1";

		$result = MysqlClient::UpdateFields(self::DB_NAME, self::BAIDU_ACCESS_TOKEN, $field_values, $condition);

		return $result;
	}
}
