<?php
// 字符串操作的相关公共方法, 目前只有按照汉字宽度截取字符串
class StringUtility {
  private function __construct() {
  }

  public static function escape ($str) {
    return mysql_real_escape_string(htmlspecialchars(trim($str),ENT_QUOTES));
  }

  /**
   * 按照汉字宽度截取字符串
   *
   * @param string  $str      input string
   * @param integer $len      length of truncated text
   * @param string  $charset        charactor
   * @param string  $suffix         end string
   * @return string truncated string
   */
  public static function SubstrCn($str, $len, $charset = "utf8", $suffix = "...") {
    // suffix是英文字符串 ... 这样的或其他英文字符串，不要是中文的
    $str_len = ($len - strlen($suffix));
    $str_len = ($str_len < 0) ? 0 : $str_len;  // 后缀长度不能大于len, 实际不会出现

    // 匹配所有的单个完整的字符和汉字
    preg_match_all(StringUtility::CharPreg($charset), $str, $match_arr);

    $flag = 0;          // 是否需要回退的标志
    $last_length  = 0;  // 加后缀后的实际宽度
    $length_total = 0;  // 转换为字符的折算宽度，汉字算2个宽度
    $char_count = 0;    // 多少个字符，汉字算1个字符长度
    foreach ($match_arr[0] as $value) {
      if (strlen($value) == 1) {
        if ($last_length < $str_len) {
          $last_length += 1;
          $char_count++;
        }
        $length_total += 1;
      } else {
        if ($last_length < $str_len) {
          if ($last_length == $str_len - 1) $flag = 1;
          $last_length += 2;
          $char_count++;
        }
        $length_total += 2;
      }
    }
    if ($flag) $char_count--;  // 回退1
    // 如果总长度小于等于截取的字符串长度，则不必加后缀
    if ($length_total <= $len) return $str;

    return join("", array_slice($match_arr[0], 0, $char_count)) . $suffix;
  }

  // 进行完整的汉字或字符匹配，防止截取半个汉字而出现乱码
  public static function CharPreg($charset = "utf8") {
    $char_arr = array();
    $char_arr['utf8']   = "/[\x01-\x7f]|[\xc0-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/e";
    $char_arr['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $char_arr['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $char_arr['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";

    if (array_key_exists($charset, $char_arr))
      return $char_arr[$charset];
    return $char_arr;
  }

  // 获取一个字符串的宽度(汉字算两个字符宽度)
  public static function GetStrWidth($str, $charset = "utf8") {
    $length_total = 0;  // 返回结果，转换为字符的折算宽度，汉字算2个宽度

    // 匹配单个完整的字符和汉字
    preg_match_all(StringUtility::CharPreg($charset), $str, $match_arr);
    if (!$match_arr[0]) return $length_total;
    foreach ($match_arr[0] as $value) {
      if (strlen($value) == 1)
        $length_total += 1;
      else
        $length_total += 2;
    }
    return $length_total;
  }

}
