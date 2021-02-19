<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifier
 */

/**
 * Smarty truncate modifier plugin
 *
 * Type:     modifier<br>
 * Name:     truncate<br>
 * Purpose:  Truncate a string to a certain length if necessary,
 *               optionally splitting in the middle of a word, and
 *               appending the $etc string or inserting $etc into the middle.
 *
 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php truncate (Smarty online manual)
 * @author Monte Ohrt <monte at ohrt dot com>
 * @param string  $a_str      input string
 * @param integer $a_len      length of truncated text
 * @param string  $a_charset        charactor
 * @param string  $a_suffix         end string
 * @return string truncated string
 */
function smarty_modifier_truncate_cn($a_str, $a_len, $a_charset="utf8", $a_suffix="...") {
  // suffix是英文字符串 ... 这样的或其他英文字符串，不要是中文的
  $l_s_len = ($a_len - strlen($a_suffix));
  $l_s_len = ($l_s_len < 0) ? 0 : $l_s_len;  // 后缀长度不能大于 a_len, 实际业务中也不会有这样的情况

  // 匹配所有的单个完整的字符和汉字
  preg_match_all(smarty_modifier_truncate_cn_char_preg($a_charset), $a_str, $l_arr);

  $l_flag  = 0;  // 是否需要回退的标志
  $l_s_num = 0;  // 加后缀后的实际宽度
  $l_total = 0;  // 转换为字符的折算宽度，汉字算2个宽度
  $l_count = 0;  // 多少个字符，汉字算1个字符长度
  foreach ($l_arr[0] as $k => $l_v) {
    if (strlen($l_v) == 1) {
      if ($l_s_num < $l_s_len) {
        $l_s_num += 1;
        $l_count++;
      }
      $l_total += 1;
    } else {
      if ($l_s_num < $l_s_len) {
        if ($l_s_num == $l_s_len-1) $l_flag = 1;
        $l_s_num += 2;
        $l_count++;
      }
      $l_total += 2;
    }
  }
  if ($l_flag) $l_count--;  // 回退1
  // 如果总长度小于等于截取的字符串长度，则不必加后缀
  if ($l_total <= $l_s_len) $a_suffix = "";

  return join("", array_slice($l_arr[0], 0, $l_count)) . $a_suffix;
}

function smarty_modifier_truncate_cn_char_preg($a_charset="utf8") {
  $l_c = array();
  $l_c['utf8']   = "/[\x01-\x7f]|[\xc0-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/e";
  $l_c['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
  $l_c['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
  $l_c['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";

  if (key_exists($a_charset, $l_c)) {
    return $l_c[$a_charset];
  } else {
    return $l_c;
  }
}
