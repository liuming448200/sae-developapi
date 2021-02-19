<?php
/**
 * Utility 基础类，不依赖其他方法，类，全局变量（$_SERVER 全局变量除外）；
 * @package   main
 * @subpackage  classes
 * @abstract  Classes defined as abstract may not be instantiated
 */
class UtilityBase {
  const AGENT_ANDROID = 'android';
  const AGENT_IPHONE = 'iphone';
  const AGENT_PC = 'pc';

  private function __construct() {

  }

  public static function GetDatabaseTime() {
    return date("Y-m-d H:i:s");
  }

  public static function GetDatabaseTimestamp() {
    return time();
  }

  public static function GetIOSTime() {
    return date('c');
  }

  public static function TimestampToDatabseDate($timestamp) {
    return self::TimestampToDatabaseDate($timestamp);
  }

  public static function TimestampToDatabaseDate($timestamp) {
    return date("Y-m-d", $timestamp);
  }

  public static function TimestampToDatabaseTime($timestamp) {
    return date("Y-m-d H:i:s", $timestamp);
  }

  public static function IsInfiniteTime($time) {
    return $time == '0000-00-00 00:00:00';
  }

  public static function IsInfiniteDate($time) {
    return $time == '0000-00-00';
  }

  public static function GetTmTime($date) {
    return strptime($date, "%Y-%m-%d %H:%M:%S");
  }

  public static function GetTime($date) {
    return strtotime($date) + 0;
  }

  // 通过时间戳得到属于周几（汉字，中文）
  public static function GetWeekCN($timestamp) {
    $day = date("N", $timestamp);
    $week = array(
      1 => "周一", 2 => "周二", 3 => "周三", 4 => "周四", 5 => "周五", 6 => "周六", 7 => "周日",
    );
    if ($day > 7 || $day < 1)
      return "未知";
    return $week[$day];
  }

  /**
   * 获取用户代理类型
   *
   * @return string 用户代理类型
   */
  public static function GetUserAgentType() {
    $user_agent = strtolower(@$_SERVER['HTTP_USER_AGENT']);
    if (false !== stripos($user_agent, 'mobile')) {
      if (false !== stripos($user_agent, 'android'))
        return self::AGENT_ANDROID;
      if (false !== stripos($user_agent, 'iphone'))
        return self::AGENT_IPHONE;
    }
    if (false !== stripos($user_agent, 'msie'))
      return self::AGENT_PC;
    if (false !== stripos($user_agent, 'chrome'))
      return self::AGENT_PC;
    if (false !== stripos($user_agent, 'firefox'))
      return self::AGENT_PC;
    if (false !== stripos($user_agent, 'android'))
      return self::AGENT_ANDROID;
    if (false !== stripos($user_agent, 'iphone'))
      return self::AGENT_IPHONE;
    return self::AGENT_PC;
  }

  /**
   * 检测http request是否是来自Apple Captive Portal类browser
   * 主要用于wanhui portal类现场项目
   *
   */
  public static function IsRequestByAppleCaptivePortal(){
    $user_agent = strtolower(@$_SERVER['HTTP_USER_AGENT']);
    return ((FALSE !== strpos($user_agent, 'captivenetworksupport'))
        && (FALSE !== strpos($user_agent, 'wispr')));
  }

  public static function GetRemoteIP() {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) {
      $ip_array = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
      foreach ($ip_array as $ip) {
        if ('unknown' != $ip)
          return $ip;
      }
    }
    return $_SERVER['REMOTE_ADDR'];
  }

  // 获取本机IP, 暂只支持Linux版本
  public static function GetLocalIP() {
    if ('WIN' !== strtoupper(substr(PHP_OS, 0, 3))) {
      $exec = "/sbin/ifconfig | grep 'inet addr' | awk '{ print $2 }' | awk -F ':' '{ print $2}' | head -1";
      return exec($exec);
    }
    return '127.0.0.1';
  }

  /**
   * 计算用户ktv之间的距离
   *
   * @return double 距离
   */
  public static function CalcDis($lon1, $lat1, $lon2, $lat2) {
    $PI = acos(-1.0);
    $rad_lat1 = $lat1 * $PI / 180;
    $rad_lat2 = $lat2 * $PI / 180;
    $a = $rad_lat1 - $rad_lat2;
    $b = $lon1 * $PI / 180 - $lon2 * $PI / 180;
    $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($rad_lat1) * cos($rad_lat2) * pow(sin($b / 2), 2)));
    $s = $s * 6378137.0;
    $s = ($s * 10000 + 0.5) * 1.0 / 10000;
    return (double) $s;
  }

  /**
   * 检验数据是否不为NULL
   */
  public static function ValidateIsSet($arr) {
    foreach ($arr as $k => $v) {
      if (!isset($v[0]))
        return $v[1];
    }
    return 0;
  }

  /**
   * 检验数据是否EMPTY
   */
  public static function ValidateIsEmpty($arr) {
    foreach ($arr as $k => $v) {
      if (empty($v[0]))
        return $v[1];
    }
    return 0;
  }

  /**
   * 检验数据是否合法
   */
  public static function Validate($arr) {
    foreach ($arr as $k => $v) {
      if (NULL === filter_var($v[0], $v[2], FILTER_NULL_ON_FAILURE))
        return $v[1];
    }
    return 0;
  }

  public static function GetUrlPrefix() {
    $uri = $_SERVER['REQUEST_URI'];
    $position = strrpos($uri, '#');
    if ($position)
      $uri = substr($uri, 0, $position);
    $position = strrpos($uri, '?');
    if ($position)
      $uri = substr($uri, 0, $position);
    return $uri;
  }

  public static function ValidateIsUserName($value, $minLen=2, $maxLen=16, $charset='')
  {
    if(empty($value))
      return false;
    switch($charset)
    {
      case 'EN':
        $match = '/^[_\w\d]{'.$minLen.','.$maxLen.'}$/iu';
        break;
      case 'CN':
        $match = '/^[_\x{4e00}-\x{9fa5}\d]{'.$minLen.','.$maxLen.'}$/iu';
        break;
      default:
        $match = '/^[_\w\d\x{4e00}-\x{9fa5}]{'.$minLen.','.$maxLen.'}$/iu';
    }
    return preg_match($match,$value);
  }

  public static function ValidateIsPhone($phone) {
    // 验证用户名是否为手机号
    return self::ValidateIsMobile($phone) ? 0 : 1;
  }

  /* 验证手机号
   * param string mobile 手机号
   * return bool true：是；false：否；
   */
  public static function ValidateIsMobile($mobile) {
    if (preg_match("/^0?(13|14|15|16|17|18)[0-9]{9}$/", $mobile))
      return TRUE;
    return FALSE;
  }

  /* 验证密码是否合法(6~16位数字字母符号)
   * param string $string 密码字符串
   * return bool true：是；false：否；
   */
  public static function ValidatePassword($string) {
    if (preg_match("/^[a-zA-Z0-9~`!@#$%^&*()_+=\-{}\[\]|:;\"'<>?,.\/\\\\]{6,16}$/", $string))
      return TRUE;
    return FALSE;
  }

  /**
   * 生成随机数
   */
  public static function GetRandNum($num) {
    $randStr = str_shuffle('1234567890');
    $rand = substr($randStr, 0, $num);
    return $rand;
  }

  // 将对象转换为多维数组
  public static function ObjectToArray($object) {
    if (is_object($object))
      $object = get_object_vars($object);
    else if (is_array($object))
      return array_map(array(__CLASS__, __FUNCTION__), $object);
    return $object;
  }

  // 获取多维数组指定key的value
  public static function GetArrayValueByKey($array, $search) {
    $search_value = array();
    $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($array));
    foreach ($iterator as $key => $value) {
      if ($iterator->getDepth() > 3)
        continue; // 深度超过3层的不再处理
      if ($search === $key)
        $search_value[$value] = $value; // 排重，保证相同图片只处理一次
    }
    return $search_value;
  }

  // 查找并修改指定项
  public static function ReplaceKeys(&$source, $search_key, &$replace_values, $default_value, $depth = 1) {
    if ($depth > 3)
      return;
    foreach ($source as $k => &$v) {
      if (is_array($v) || is_object($v))
        self::ReplaceKeys($v, $search_key, $replace_values, $default_value, $depth + 1);

      if ($k === $search_key) {
        if (is_array($source))
          self::ReplaceArrayKeys($source, $replace_values, $default_value, $k, $v);
        else
          self::ReplaceObjectKeys($source, $replace_values, $default_value, $k, $v);
      }
    }
  }

  public static function ReplaceArrayKeys(&$array, $replace_values, $default_value, $search_key, $search_values) {
    if (@isset($replace_values[$search_values]))
      $array[$search_key] = $replace_values[$search_values];
    else
      $array[$search_key] = $default_value;
  }

  public static function ReplaceObjectKeys(&$object, $replace_values, $default_value, $search_key, $search_values) {
    if (@isset($replace_values[$search_values]))
      $object->$search_key = $replace_values[$search_values];
    else
      $object->$search_key = $default_value;
  }

  /* 检查字符串是否date类型
   * param string $string
   * return bool
   */
  public static function StringIsDate($string) {
    $date = explode("-", $string);
    if (count($date) !== 3)
      return FALSE;
    return checkdate($date[1], $date[2], $date[0]);
  }

  /* 检查生日是否合法
   * param string $birthday (类型：date)
   * return bool
   */
  public static function StringIsBirthday($birthday) {
    if (!self::StringIsDate($birthday))
      return FALSE;

    return $birthday <= date('Y-m-d');
  }

  /**
   * 把数组的数组按键集合排序
   * @param array $arr_keys
   * @param array $arr_arr
   * @param string $key_name
   * @return array[$key => $item] 返回数组比原来多了键
   */
  public static function keepArrayOrder($arr_keys, $arr_arr, $key_name) {
    $result = array();
    $tmp = array();
    //判断参数是否存在
    if (empty($arr_keys) || empty($arr_arr) || empty($key_name))
      return $arr_arr;
    // 判断键是否存在
    if (!isset($arr_arr[0][$key_name]))
      return $arr_arr;

    // FIXME 返回数组比原来多了键
    foreach ($arr_arr as $value) {
      $tmp[$value[$key_name]] = $value;
    }
    foreach ($arr_keys as $key) {
      if (!empty($tmp[$key])) {
        $result[$key] = $tmp[$key];
      }
    }
    return $result;
  }

  /**
   * 把对象的数组按键集合排序
   * @param array $arr_keys
   * @param array $arr_objs
   * @param string $key_name
   * @return array[$key => $item] 返回数组比原来多了键
   */
  public static function keepObjsOrder($arr_keys, $arr_objs, $key_name) {
    $result = array();
    $tmp = array();
    // 判断参数是否存在
    if (empty($arr_keys) || empty($arr_objs) || empty($key_name))
      return $arr_objs;
    // 判断键是否存在
    if (!isset($arr_objs[0]->$key_name))
      return $arr_objs;

    foreach ($arr_objs as $object) {
      $tmp[$object->$key_name] = $object;
    }
    foreach ($arr_keys as $key) {
      if (!empty($tmp[$key])) {
        $result[$key] = $tmp[$key];
      }
    }
    return $result;
  }
}
