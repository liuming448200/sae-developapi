<?php

class suggestionDaoImpl {

  const DB_NAME = SAE_MYSQL_DB;

  const TABLE_USER_SUGGESTION = 'web_user_suggestion';

  public function createSuggestionRow ($content, $contact) {
    $row = array(
      'content' => $content,
      'contact' => $contact
    );

    $fields = array_keys($row);

    $rows = array($row);

    $result = MysqlClient::InsertData(self::DB_NAME, self::TABLE_USER_SUGGESTION, $fields, $rows);

    return $result ? MysqlClient::GetInsertID(self::DB_NAME) : 0;
  }
}
