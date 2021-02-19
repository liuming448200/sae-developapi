<?php
require(PHP_ROOT . 'libs/util/UtilityBase.php');
require(PHP_ROOT . 'libs/util/HttpHandlerCurl.php');
require(PHP_ROOT . 'libs/util/Session.php');

class Utility extends UtilityBase {

  const SESSION_UID_KEY = 'uid';
  const SESSION_UINFO_KEY = 'session_user_info';
  const FILE_NAME = 'filename';
  const FILE_SIZE = 'filesize';
  const FILE_DATA = 'filedata';
  const FILE_LAST_MODIFIED = 'filelastmodified';
  const PARTNER_QQ_TIME_OUT = 2;

  const UCENTER_KEYWORD_TYPE = 0;
  //const UCENTER_APPID = 'crm';
  const UCENTER_APPID = 'sso';

  private function __construct() {

  }

  public static function GetUserInfoFromSession() {
    return @json_decode($_SESSION[self::SESSION_UINFO_KEY], true);
  }

  public static function SetUserInfoToSession($user_info, $accessToken = false) {
    if ($accessToken != false) {
      $user_info['accessToken'] = $accessToken;
    } else {
      $old_user_info = self::GetUserInfoFromSession();
      $user_info['accessToken'] = @$old_user_info['accessToken'];
    }

    $_SESSION[self::SESSION_UINFO_KEY] = json_encode($user_info);

    return true;
  }

  public static function BindSessionUid($uid) {
    $_SESSION[self::SESSION_UID_KEY] = $uid;
    $key = Session::SESSION_PREFIX . session_id();
    $memcached_client_ = SessionMemCachedClient::GetInstance();
    if ($memcached_client_->get($key) || $memcached_client_->LatestGetCacheAvailable())
      return true;
    return false;
  }

  public static function CheckSessionValid($uid) {
    $session_uid = @$_SESSION[self::SESSION_UID_KEY];
    if ($session_uid)
      return $session_uid == $uid;
    return false;
  }

  public static function LoadFile($file_path) {
    if (!file_exists($file_path)) {
      // Logg::Warn('File not exists:' . $file_path);
      return false;
    }
    $result = array();
    $result[self::FILE_NAME] = basename($file_path);
    $result[self::FILE_SIZE] = filesize($file_path);
    $result[self::FILE_LAST_MODIFIED] = filemtime($file_path);
    $file = fopen($file_path, "r");
    $result[self::FILE_DATA] = fread($file, $result[self::FILE_SIZE]);
    fclose($file);
    return $result;
  }

  /**
   * 拼装用户图像url的前缀. http://domain/(small|middle|big|orig)/MD5 如:
   *   http://i[0-3].domain.com/small/picname, 以后可以随机获取一个id进行拼装前缀
   *
   * @param string $filename 图片文件名
   * @param string $type 图片文件类型，主要是大小类型
   * @return string 完整的图片url
   */
  public static function GetUserPicsrcPre($filename, $type) {
    // 图片类型当前只有这几种, 见文档《万达电商图片存储设计文档》
    $type_arr = array('singsmall', 'singmiddle', 'singbig', 'orig');

    $filename = trim($filename);
    if (!$filename || !in_array($type, $type_arr))
      return ''; // 没有文件名直接返回空url


// 需要依据 PIC_PRE_SERVER_NUM 的数值进行判断
    $num = IMAGE_DOMAIN_NUM + 0;
    if ($num > 1) {
      // 图片文件名首字母ascll码用图片服务器数量取模
      $i = ord(substr($filename, 0, 1)) % $num;
      return 'http://i' . $i . '.' . IMAGE_DOMAIN_SUFFIX . '/' . $type . '/' . $filename;
    }
    return 'http://' . IMAGE_DOMAIN_SUFFIX . '/' . $type . '/' . $filename;
  }

  /**
   * 拼装用户歌曲url. http://domain/<$siid> 如:
   * http://k.dagexing.com/1
   *
   * @param string $siid 用户歌曲id
   * @return string 完整的歌曲url
   */
  public static function GetSongUrl($siid) {
    return 'http://' . SONG_SERVER . '/song/' . $siid;
  }

  // 从qq的接口拿到数据
  public static function GetPartnerInfo($oauthuid, $oauthtoken) {
    $api_url = "https://graph.qq.com/user/get_vip_rich_info?"
            . "oauth_consumer_key=" . QQ_AKEY
            . "&access_token=" . $oauthtoken
            . "&openid=" . $oauthuid
            . "&format=json";
    // Logg::Debug('api_url: ' . $api_url);

    // 通过GET请求数据
    $curl = new Curl();
    $curl->SetTimeout(self::PARTNER_QQ_TIME_OUT);
    $content = $curl->get($api_url);
    // 网络问题返回false
    if (!$content)
      return false;

    // 进行json解码
    $json_obj = json_decode($content);

    // 防止获取的数据不是json格式
    if (!$json_obj)
      return false;

    // 获取到会员卡信息
    if (0 != $json_obj->ret) {
      // Logg::Warn('qq oauth error msg: ' . $json_obj->msg);
      return false;
    }
    return $json_obj;
  }

    //生成验证码
  public static function GetImgCode($imgkey = 'img_code', $imagetype='gif', $width = 120, $height = 35) {
    $str = 'abcdefhjkmnpqrstwxy2345679ABCDEFHGKLMNPRSTUVWXTZ';
    $tmp = strlen($str)-1;
    $code = $str[rand(0,$tmp)].$str[rand(0,$tmp)].$str[rand(0,$tmp)].$str[rand(0,$tmp)];
    $len = strlen($code);
    $size = $width/$len;
    if ($size > $height) {
      $size=$height-30;
    } else {
      $size=$height-10;
    }
    $left = 5;
    $image = imageCreate($width, $height);
    $back = imagecolorallocate ($image,'255', '255', '255');
    imageFilledRectangle($image, 0, 0, $width, $height, $back);
    //随机数
    for ($i=0; $i<$len; $i++) {
      $randtext = $code[$i];
      $textColor = imageColorAllocate($image , rand(0, 100), rand(0, 100), rand(0, 100));
      $randsize = rand($size-$size/12, $size+$size/12);
      $location = $left+($i*$size+$size/10);
      imagettftext($image, $randsize, rand(-10,10), $location, rand($size-$size/10, $size+$size/10), $textColor, IMG_TTF_PATH, $randtext);
    }
    //加干扰点
    $noise = true;
    if ($noise) {
      $noisenum = rand(300,500);
      for ($i=0; $i<$noisenum; $i++) {
        $randColor = imageColorAllocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
        imageSetPixel($image, rand(0, $width), rand(0, $height), $randColor);
      }
    }
    //加干扰线
    $noiseline=true;
    if ($noiseline) {
      $baseheight=  $height/2;
      $startRadiu=rand(0,1)+M_PI/3;
      $startRadiu2= rand(0,1)+M_PI/3;;
      for($i=0; $i<$width; $i++) {
        $color = imageColorAllocate($image, 0,0,0);
        imageSetPixel($image, $i, cos($startRadiu-$i/$width*M_PI)*$baseheight, $color);
        imageSetPixel($image, $i, tan($startRadiu2-$i/$width*M_PI)*$baseheight, $color);
      }
    }
    header('Content-Type: image/'.$imagetype);
    switch (strtolower($imagetype)) {
      case "jpg":
        imageJpeg($image);
        break;
      case "png":
        imagePng($image);
        break;
      case "gif":
        imageGif($image);
        break;
      default:
        imageJpeg($image);
        break;
    }
    $_SESSION[$imgkey] = strtolower($code);
    imagedestroy($image);
    exit();
  }

  /**
   * 获取URL返回的内容
   * @param $url 地址
   * @param $con_timeout 连接超时时间
   * @param $read_timeout 请求超时时间
   * @param $method 请求方式 GET/POST
   * @param $postdata 发送post数据
   * @param $cookie 发送Cookie
   * @param $compressed 是否压缩 false/true
   * @return $content string
   */
  public static function UrlGetContents($url, $con_timeout=6, $read_timeout=6, $method='GET', $postdata=array(), $cookie = array(), $compressed=false) {
    $cookie_string = '';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $con_timeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $read_timeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    if ($method == 'POST' && $postdata) {
      curl_setopt($ch, CURLOPT_POST, count($postdata));
      curl_setopt($ch, CURLOPT_POSTFIELDS,$postdata);
    }
    if ($cookie) {
      foreach ($cookie as $k => $v)
        $cookie_string .= $k . '=' . $v . '; ';
      curl_setopt($ch, CURLOPT_COOKIE, $cookie_string);
    }
    if ($compressed) {
      curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    }

    $content = curl_exec($ch);
    curl_close($ch);

    return $content;
  }

 /**
  * 数组key值风格转换
  * type 0 将Java风格转换为C的风格， 1 将C风格转换为Java的风格
  * @param array $result 需要转换的数组
  * @param number $case 转换类型，默认小写
  */
  public static function ArrayKeyToCase($result, $case=0) {
	$temp = array();
	foreach ( $result as $key => $item ) {
		if ( $case ) {
			$keyTemp = (preg_replace("/_([a-zA-Z])/e", "strtoupper('\\1')", $key));
		} else {
			$keyTemp = strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $key), "_"));
		}
		$temp[$keyTemp] = $item;
		if ( is_array($item) ) {
			$temp[$keyTemp] = self::ArrayKeyToCase($item, $case);
		}
	}
	return $temp;
  }
}
