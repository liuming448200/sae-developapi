<?php

class searchDaoImpl {

  const DB_NAME = SAE_MYSQL_DB;

  const TABLE_CONTENT_TYPE = 'content_type'; //内容分类表
	const TABLE_STORY_BASE_INFO = 'story_base_info'; //故事基本信息表
	const TABLE_SONG_BASE_INFO_CN = 'song_base_info_cn'; //中文儿歌基本信息表
  const TABLE_SONG_BASE_INFO_EN = 'song_base_info_en'; //英文儿歌基本信息表
  const TABLE_WORD_BASE_INFO = 'word_base_info'; //单词基本信息表

  public function getRelatedResult ($keyword) {
    $where = " where name like '%" . addslashes($keyword) . "%' ";
    $sql = 'select a.name, b.typename from (';

  	$sql .= 'select name, tid from ' . self::TABLE_STORY_BASE_INFO . $where . 'union all';
    $sql .= ' select name, tid from ' . self::TABLE_SONG_BASE_INFO_CN . $where . 'union all';
    $sql .= ' select name, tid from ' . self::TABLE_SONG_BASE_INFO_EN . $where . 'union all';
    $condition = " where english like '%" . addslashes($keyword) . "%' or chinese like '%" . addslashes($keyword) . "%' ";
    $sql .= ' select english, tid from ' . self::TABLE_WORD_BASE_INFO . $condition;
    $sql .= ' ) a ';

    $on = ' b on a.tid = b.tid';
    $sql .= 'join ' . self::TABLE_CONTENT_TYPE . $on;

    $result = MysqlClient::ExecuteQuery(self::DB_NAME, $sql);

    return $result;
  }

  public function getExactResult ($keyword) {
  	$where = " where name='$keyword' ";
    $sql = 'select story_id, name, tid from ' . self::TABLE_STORY_BASE_INFO . $where . 'union all';
    $sql .= ' select song_id, name, tid from ' . self::TABLE_SONG_BASE_INFO_CN . $where . 'union all';
    $sql .= ' select song_id, name, tid from ' . self::TABLE_SONG_BASE_INFO_EN . $where . 'union all';
    $condition = " where english='$keyword' or chinese='$keyword' ";
    $sql .= ' select word_id, english, tid from ' . self::TABLE_WORD_BASE_INFO . $condition;

    $result = MysqlClient::ExecuteQuery(self::DB_NAME, $sql);

    return $result;
  }
}
