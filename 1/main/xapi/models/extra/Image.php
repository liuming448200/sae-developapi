<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id: Image.class.php 2601 2012-01-15 04:59:14Z liu21st $

/**
 +------------------------------------------------------------------------------
* 图像操作类库
+------------------------------------------------------------------------------
* @category   ORG
* @package  ORG
* @subpackage  Util
* @author    liu21st <liu21st@gmail.com>
* @version   $Id: Image.class.php 2601 2012-01-15 04:59:14Z liu21st $
+------------------------------------------------------------------------------
*/
class Image {

    /**
     * 保持生产验证码的字符.
     */
    private static $_verifyCode = '';

    public static function getVcode(){
        return self::$_verifyCode;
    }

	/**
	 +----------------------------------------------------------
	 * 取得图像信息
	 *
	 +----------------------------------------------------------
	 * @static
	 * @access public
	 +----------------------------------------------------------
	 * @param string $image 图像文件名
	 +----------------------------------------------------------
	 * @return mixed
	 +----------------------------------------------------------
	 */

	static function getImageInfo($img) {
		$imageInfo = getimagesize($img);
		if ($imageInfo !== false) {
			$imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
			$imageSize = filesize($img);
			$info = array(
					"width" => $imageInfo[0],
					"height" => $imageInfo[1],
					"type" => $imageType,
					"size" => $imageSize,
					"mime" => $imageInfo['mime']
			);
			return $info;
		} else {
			return false;
		}
	}

	/**
	 +----------------------------------------------------------
	 * 为图片添加水印
	 +----------------------------------------------------------
	 * @static public
	 +----------------------------------------------------------
	 * @param string $source 原文件名
	 * @param string $water  水印图片
	 * @param string $$savename  添加水印后的图片名
	 * @param string $alpha  水印的透明度
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 * @throws ThinkExecption
	 +----------------------------------------------------------
	 */
	static public function water($source, $water, $savename=null, $alpha=80) {
		//检查文件是否存在
		if (!file_exists($source) || !file_exists($water))
			return false;

		//图片信息
		$sInfo = self::getImageInfo($source);
		$wInfo = self::getImageInfo($water);

		//如果图片小于水印图片，不生成图片
		if ($sInfo["width"] < $wInfo["width"] || $sInfo['height'] < $wInfo['height'])
			return false;

		//建立图像
		$sCreateFun = "imagecreatefrom" . $sInfo['type'];
		$sImage = $sCreateFun($source);
		$wCreateFun = "imagecreatefrom" . $wInfo['type'];
		$wImage = $wCreateFun($water);

		//设定图像的混色模式
		imagealphablending($wImage, true);

		//图像位置,默认为右下角右对齐
		$posY = $sInfo["height"] - $wInfo["height"];
		$posX = $sInfo["width"] - $wInfo["width"];

		//生成混合图像
		imagecopymerge($sImage, $wImage, $posX, $posY, 0, 0, $wInfo['width'], $wInfo['height'], $alpha);

		//输出图像
		$ImageFun = 'Image' . $sInfo['type'];
		//如果没有给出保存文件名，默认为原图像名
		if (!$savename) {
			$savename = $source;
			@unlink($source);
		}
		//保存图像
		$ImageFun($sImage, $savename);
		imagedestroy($sImage);
	}

	function showImg($imgFile, $text='', $x='10', $y='10', $alpha='50') {
		//获取图像文件信息
		//2007/6/26 增加图片水印输出，$text为图片的完整路径即可
		$info = Image::getImageInfo($imgFile);
		if ($info !== false) {
			$createFun = str_replace('/', 'createfrom', $info['mime']);
			$im = $createFun($imgFile);
			if ($im) {
				$ImageFun = str_replace('/', '', $info['mime']);
				//水印开始
				if (!empty($text)) {
					$tc = imagecolorallocate($im, 0, 0, 0);
					if (is_file($text) && file_exists($text)) {//判断$text是否是图片路径
						// 取得水印信息
						$textInfo = Image::getImageInfo($text);
						$createFun2 = str_replace('/', 'createfrom', $textInfo['mime']);
						$waterMark = $createFun2($text);
						//$waterMark=imagecolorallocatealpha($text,255,255,0,50);
						$imgW = $info["width"];
						$imgH = $info["width"] * $textInfo["height"] / $textInfo["width"];
						//$y	=	($info["height"]-$textInfo["height"])/2;
						//设置水印的显示位置和透明度支持各种图片格式
						imagecopymerge($im, $waterMark, $x, $y, 0, 0, $textInfo['width'], $textInfo['height'], $alpha);
					} else {
						imagestring($im, 80, $x, $y, $text, $tc);
					}
					//ImageDestroy($tc);
				}
				//水印结束
				if ($info['type'] == 'png' || $info['type'] == 'gif') {
					imagealphablending($im, FALSE); //取消默认的混色模式
					imagesavealpha($im, TRUE); //设定保存完整的 alpha 通道信息
				}
				Header("Content-type: " . $info['mime']);
				$ImageFun($im);
				@ImageDestroy($im);
				return;
			}

			//保存图像
			$ImageFun($sImage, $savename);
			imagedestroy($sImage);
			//获取或者创建图像文件失败则生成空白PNG图片
			$im = imagecreatetruecolor(80, 30);
			$bgc = imagecolorallocate($im, 255, 255, 255);
			$tc = imagecolorallocate($im, 0, 0, 0);
			imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
			imagestring($im, 4, 5, 5, "no pic", $tc);
			Image::output($im);
			return;
		}
	}

	/**
	 +----------------------------------------------------------
	 * 生成缩略图
	 +----------------------------------------------------------
	 * @static
	 * @access public
	 +----------------------------------------------------------
	 * @param string $image  原图
	 * @param string $type 图像格式
	 * @param string $thumbname 缩略图文件名
	 * @param string $maxWidth  宽度
	 * @param string $maxHeight  高度
	 * @param string $position 缩略图保存目录
	 * @param boolean $interlace 启用隔行扫描
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 */
	static function thumb($image, $thumbname, $type='', $maxWidth=200, $maxHeight=50, $interlace=true) {
		// 获取原图信息
		$info = Image::getImageInfo($image);
		if ($info !== false) {
			$srcWidth = $info['width'];
			$srcHeight = $info['height'];
			$type = empty($type) ? $info['type'] : $type;
			$type = strtolower($type);
			$interlace = $interlace ? 1 : 0;
			unset($info);
			$scale = min($maxWidth / $srcWidth, $maxHeight / $srcHeight); // 计算缩放比例
			if ($scale >= 1) {
				// 超过原图大小不再缩略
				$width = $srcWidth;
				$height = $srcHeight;
			} else {
				// 缩略图尺寸
				$width = (int) ($srcWidth * $scale);
				$height = (int) ($srcHeight * $scale);
			}

			// 载入原图
			$createFun = 'ImageCreateFrom' . ($type == 'jpg' ? 'jpeg' : $type);
			$srcImg = $createFun($image);

			//创建缩略图
			if ($type != 'gif' && function_exists('imagecreatetruecolor'))
				$thumbImg = imagecreatetruecolor($width, $height);
			else
				$thumbImg = imagecreate($width, $height);

			// 复制图片
			if (function_exists("ImageCopyResampled"))
				imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
			else
				imagecopyresized($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
			if ('gif' == $type || 'png' == $type) {
				//imagealphablending($thumbImg, false);//取消默认的混色模式
				//imagesavealpha($thumbImg,true);//设定保存完整的 alpha 通道信息
				$background_color = imagecolorallocate($thumbImg, 0, 255, 0);  //  指派一个绿色
				imagecolortransparent($thumbImg, $background_color);  //  设置为透明色，若注释掉该行则输出绿色的图
			}

			// 对jpeg图形设置隔行扫描
			if ('jpg' == $type || 'jpeg' == $type)
				imageinterlace($thumbImg, $interlace);

			//$gray=ImageColorAllocate($thumbImg,255,0,0);
			//ImageString($thumbImg,2,5,5,"ThinkPHP",$gray);
			// 生成图片
			$imageFun = 'image' . ($type == 'jpg' ? 'jpeg' : $type);
			$imageFun($thumbImg, $thumbname);
			imagedestroy($thumbImg);
			imagedestroy($srcImg);
			return $thumbname;
		}
		return false;
	}

	/**
	 +----------------------------------------------------------
	 * 根据给定的字符串生成图像
	 +----------------------------------------------------------
	 * @static
	 * @access public
	 +----------------------------------------------------------
	 * @param string $string  字符串
	 * @param string $size  图像大小 width,height 或者 array(width,height)
	 * @param string $font  字体信息 fontface,fontsize 或者 array(fontface,fontsize)
	 * @param string $type 图像格式 默认PNG
	 * @param integer $disturb 是否干扰 1 点干扰 2 线干扰 3 复合干扰 0 无干扰
	 * @param bool $border  是否加边框 array(color)
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	static function buildString($string, $rgb=array(), $filename='', $type='png', $disturb=1, $border=true) {
		if (is_string($size))
			$size = explode(',', $size);
		$width = $size[0];
		$height = $size[1];
		if (is_string($font))
			$font = explode(',', $font);
		$fontface = $font[0];
		$fontsize = $font[1];
		$length = strlen($string);
		$width = ($length * 9 + 10) > $width ? $length * 9 + 10 : $width;
		$height = 22;
		if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
			$im = @imagecreatetruecolor($width, $height);
		} else {
			$im = @imagecreate($width, $height);
		}
		if (empty($rgb)) {
			$color = imagecolorallocate($im, 102, 104, 104);
		} else {
			$color = imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);
		}
		$backColor = imagecolorallocate($im, 255, 255, 255);    //背景色（随机）
		$borderColor = imagecolorallocate($im, 100, 100, 100);                    //边框色
		$pointColor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));                 //点颜色

		@imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
		@imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);
		@imagestring($im, 5, 5, 3, $string, $color);
		if (!empty($disturb)) {
			// 添加干扰
			if ($disturb = 1 || $disturb = 3) {
				for ($i = 0; $i < 25; $i++) {
					imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $pointColor);
				}
			} elseif ($disturb = 2 || $disturb = 3) {
				for ($i = 0; $i < 10; $i++) {
					imagearc($im, mt_rand(-10, $width), mt_rand(-10, $height), mt_rand(30, 300), mt_rand(20, 200), 55, 44, $pointColor);
				}
			}
		}
		Image::output($im, $type, $filename);
	}

	/**
	 +----------------------------------------------------------
	 * 生成图像验证码
	 +----------------------------------------------------------
	 * @static
	 * @access public
	 +----------------------------------------------------------
	 * @param string $length  位数
	 * @param string $mode  类型
	 * @param string $type 图像格式
	 * @param string $width  宽度
	 * @param string $height  高度
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	static function buildImageVerify($length=4, $mode=1, $type='png', $width=48, $height=22, $verifyName='verify') {
		 
		$randval = String::randString($length, $mode);
        self::$_verifyCode = md5($randval);
		//$_SESSION[$verifyName] = md5($randval);
		$width = ($length * 10 + 10) > $width ? $length * 10 + 10 : $width;
		if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
			$im = imagecreatetruecolor($width, $height);
		} else {
			$im = imagecreate($width, $height);
		}
		$r = Array(225, 255, 255, 223);
		$g = Array(225, 236, 237, 255);
		$b = Array(225, 236, 166, 125);
		$key = mt_rand(0, 3);

		$backColor = imagecolorallocate($im, $r[$key], $g[$key], $b[$key]);    //背景色（随机）
		$borderColor = imagecolorallocate($im, 100, 100, 100);                    //边框色
		imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
		imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);
		$stringColor = imagecolorallocate($im, mt_rand(0, 200), mt_rand(0, 120), mt_rand(0, 120));
		// 干扰
		for ($i = 0; $i < 10; $i++) {
			imagearc($im, mt_rand(-10, $width), mt_rand(-10, $height), mt_rand(30, 300), mt_rand(20, 200), 55, 44, $stringColor);
		}
		for ($i = 0; $i < 25; $i++) {
			imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $stringColor);
		}
		for ($i = 0; $i < $length; $i++) {
			imagestring($im, 5, $i * 10 + 5, mt_rand(1, 8), $randval{$i}, $stringColor);
		}
		Image::output($im, $type);
	}

	// 中文验证码
	static function GBVerify($length=4, $type='png', $width=180, $height=50, $fontface='simhei.ttf', $verifyName='verify') {
		import('ORG.Util.String');
		$code = String::randString($length, 4);
		$width = ($length * 45) > $width ? $length * 45 : $width;
        self::$_verifyCode = md5($code);
		//$_SESSION[$verifyName] = md5($code);
		$im = imagecreatetruecolor($width, $height);
		$borderColor = imagecolorallocate($im, 100, 100, 100);                    //边框色
		$bkcolor = imagecolorallocate($im, 250, 250, 250);
		imagefill($im, 0, 0, $bkcolor);
		@imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);
		// 干扰
		for ($i = 0; $i < 15; $i++) {
			$fontcolor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
			imagearc($im, mt_rand(-10, $width), mt_rand(-10, $height), mt_rand(30, 300), mt_rand(20, 200), 55, 44, $fontcolor);
		}
		for ($i = 0; $i < 255; $i++) {
			$fontcolor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
			imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $fontcolor);
		}
		if (!is_file($fontface)) {
			$fontface = dirname(__FILE__) . "/" . $fontface;
		}
		for ($i = 0; $i < $length; $i++) {
			$fontcolor = imagecolorallocate($im, mt_rand(0, 120), mt_rand(0, 120), mt_rand(0, 120)); //这样保证随机出来的颜色较深。
			$codex = String::msubstr($code, $i, 1);
			imagettftext($im, mt_rand(16, 20), mt_rand(-60, 60), 40 * $i + 20, mt_rand(30, 35), $fontcolor, $fontface, $codex);
		}
		Image::output($im, $type);
	}

	/**
	 +----------------------------------------------------------
	 * 把图像转换成字符显示
	 +----------------------------------------------------------
	 * @static
	 * @access public
	 +----------------------------------------------------------
	 * @param string $image  要显示的图像
	 * @param string $type  图像类型，默认自动获取
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	static function showASCIIImg($image, $string='', $type='') {
		$info = Image::getImageInfo($image);
		if ($info !== false) {
			$type = empty($type) ? $info['type'] : $type;
			unset($info);
			// 载入原图
			$createFun = 'ImageCreateFrom' . ($type == 'jpg' ? 'jpeg' : $type);
			$im = $createFun($image);
			$dx = imagesx($im);
			$dy = imagesy($im);
			$i = 0;
			$out = '<span style="padding:0px;margin:0;line-height:100%;font-size:1px;">';
			set_time_limit(0);
			for ($y = 0; $y < $dy; $y++) {
				for ($x = 0; $x < $dx; $x++) {
					$col = imagecolorat($im, $x, $y);
					$rgb = imagecolorsforindex($im, $col);
					$str = empty($string) ? '*' : $string[$i++];
					$out .= sprintf('<span style="margin:0px;color:#%02x%02x%02x">' . $str . '</span>', $rgb['red'], $rgb['green'], $rgb['blue']);
				}
				$out .= "<br>\n";
			}
			$out .= '</span>';
			imagedestroy($im);
			return $out;
		}
		return false;
	}

	/**
	 +----------------------------------------------------------
	 * 生成高级图像验证码
	 +----------------------------------------------------------
	 * @static
	 * @access public
	 +----------------------------------------------------------
	 * @param string $type 图像格式
	 * @param string $width  宽度
	 * @param string $height  高度
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	static function showAdvVerify($type='png', $width=180, $height=40, $verifyName='verifyCode') {
		$rand = range('a', 'z');
		shuffle($rand);
		$verifyCode = array_slice($rand, 0, 10);
		$letter = implode(" ", $verifyCode);
        self::$_verifyCode = md5($verifyCode);
		//$_SESSION[$verifyName] = $verifyCode;
		$im = imagecreate($width, $height);
		$r = array(225, 255, 255, 223);
		$g = array(225, 236, 237, 255);
		$b = array(225, 236, 166, 125);
		$key = mt_rand(0, 3);
		$backColor = imagecolorallocate($im, $r[$key], $g[$key], $b[$key]);
		$borderColor = imagecolorallocate($im, 100, 100, 100);                    //边框色
		imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
		imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);
		$numberColor = imagecolorallocate($im, 255, rand(0, 100), rand(0, 100));
		$stringColor = imagecolorallocate($im, rand(0, 100), rand(0, 100), 255);
		// 添加干扰
		/*
		 for($i=0;$i<10;$i++){
		$fontcolor=imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
		imagearc($im,mt_rand(-10,$width),mt_rand(-10,$height),mt_rand(30,300),mt_rand(20,200),55,44,$fontcolor);
		}
		for($i=0;$i<255;$i++){
		$fontcolor=imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
		imagesetpixel($im,mt_rand(0,$width),mt_rand(0,$height),$fontcolor);
		} */
		imagestring($im, 5, 5, 1, "0 1 2 3 4 5 6 7 8 9", $numberColor);
		imagestring($im, 5, 5, 20, $letter, $stringColor);
		Image::output($im, $type);
	}

	/**
	 +----------------------------------------------------------
	 * 生成UPC-A条形码
	 +----------------------------------------------------------
	 * @static
	 +----------------------------------------------------------
	 * @param string $type 图像格式
	 * @param string $type 图像格式
	 * @param string $lw  单元宽度
	 * @param string $hi   条码高度
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	static function UPCA($code, $type='png', $lw=2, $hi=100) {
		static $Lencode = array('0001101', '0011001', '0010011', '0111101', '0100011',
				'0110001', '0101111', '0111011', '0110111', '0001011');
		static $Rencode = array('1110010', '1100110', '1101100', '1000010', '1011100',
				'1001110', '1010000', '1000100', '1001000', '1110100');
		$ends = '101';
		$center = '01010';
		/* UPC-A Must be 11 digits, we compute the checksum. */
		if (strlen($code) != 11) {
			die("UPC-A Must be 11 digits.");
		}
		/* Compute the EAN-13 Checksum digit */
		$ncode = '0' . $code;
		$even = 0;
		$odd = 0;
		for ($x = 0; $x < 12; $x++) {
			if ($x % 2) {
				$odd += $ncode[$x];
			} else {
				$even += $ncode[$x];
			}
		}
		$code.= ( 10 - (($odd * 3 + $even) % 10)) % 10;
		/* Create the bar encoding using a binary string */
		$bars = $ends;
		$bars.=$Lencode[$code[0]];
		for ($x = 1; $x < 6; $x++) {
			$bars.=$Lencode[$code[$x]];
		}
		$bars.=$center;
		for ($x = 6; $x < 12; $x++) {
			$bars.=$Rencode[$code[$x]];
		}
		$bars.=$ends;
		/* Generate the Barcode Image */
		if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
			$im = imagecreatetruecolor($lw * 95 + 30, $hi + 30);
		} else {
			$im = imagecreate($lw * 95 + 30, $hi + 30);
		}
		$fg = ImageColorAllocate($im, 0, 0, 0);
		$bg = ImageColorAllocate($im, 255, 255, 255);
		ImageFilledRectangle($im, 0, 0, $lw * 95 + 30, $hi + 30, $bg);
		$shift = 10;
		for ($x = 0; $x < strlen($bars); $x++) {
			if (($x < 10) || ($x >= 45 && $x < 50) || ($x >= 85)) {
				$sh = 10;
			} else {
				$sh = 0;
			}
			if ($bars[$x] == '1') {
				$color = $fg;
			} else {
				$color = $bg;
			}
			ImageFilledRectangle($im, ($x * $lw) + 15, 5, ($x + 1) * $lw + 14, $hi + 5 + $sh, $color);
		}
		/* Add the Human Readable Label */
		ImageString($im, 4, 5, $hi - 5, $code[0], $fg);
		for ($x = 0; $x < 5; $x++) {
			ImageString($im, 5, $lw * (13 + $x * 6) + 15, $hi + 5, $code[$x + 1], $fg);
			ImageString($im, 5, $lw * (53 + $x * 6) + 15, $hi + 5, $code[$x + 6], $fg);
		}
		ImageString($im, 4, $lw * 95 + 17, $hi - 5, $code[11], $fg);
		/* Output the Header and Content. */
		Image::output($im, $type);
	}

	static function output($im, $type='png', $filename='') {
		ob_clean(); //防止出现'图像因其本身有错无法显示'的问题
		header("Content-type: image/" . $type);
		$ImageFun = 'image' . $type;
		if (empty($filename)) {
			$ImageFun($im);
		} else {
			$ImageFun($im, $filename);
		}
		imagedestroy($im);
	}

}


class String {

	/**
	 +----------------------------------------------------------
	 * 生成UUID 单机使用
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	static public function uuid() {
		$charid = md5(uniqid(mt_rand(), true));
		$hyphen = chr(45);// "-"
		$uuid = chr(123)// "{"
		.substr($charid, 0, 8).$hyphen
		.substr($charid, 8, 4).$hyphen
		.substr($charid,12, 4).$hyphen
		.substr($charid,16, 4).$hyphen
		.substr($charid,20,12)
		.chr(125);// "}"
		return $uuid;
	}

	/**
	 +----------------------------------------------------------
	 * 生成Guid主键
	 +----------------------------------------------------------
	 * @return Boolean
	 +----------------------------------------------------------
	 */
	static public function keyGen() {
		return str_replace('-','',substr(String::uuid(),1,-1));
	}

	/**
	 +----------------------------------------------------------
	 * 检查字符串是否是UTF8编码
	 +----------------------------------------------------------
	 * @param string $string 字符串
	 +----------------------------------------------------------
	 * @return Boolean
	 +----------------------------------------------------------
	 */
	static public function isUtf8($str) {
		$c=0; $b=0;
		$bits=0;
		$len=strlen($str);
		for($i=0; $i<$len; $i++){
			$c=ord($str[$i]);
			if($c > 128){
				if(($c >= 254)) return false;
				elseif($c >= 252) $bits=6;
				elseif($c >= 248) $bits=5;
				elseif($c >= 240) $bits=4;
				elseif($c >= 224) $bits=3;
				elseif($c >= 192) $bits=2;
				else return false;
				if(($i+$bits) > $len) return false;
				while($bits > 1){
					$i++;
					$b=ord($str[$i]);
					if($b < 128 || $b > 191) return false;
					$bits--;
				}
			}
		}
		return true;
	}

	/**
	 +----------------------------------------------------------
	 * 字符串截取，支持中文和其他编码
	 +----------------------------------------------------------
	 * @static
	 * @access public
	 +----------------------------------------------------------
	 * @param string $str 需要转换的字符串
	 * @param string $start 开始位置
	 * @param string $length 截取长度
	 * @param string $charset 编码格式
	 * @param string $suffix 截断显示字符
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	static public function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
		if(function_exists("mb_substr"))
			$slice = mb_substr($str, $start, $length, $charset);
		elseif(function_exists('iconv_substr')) {
			$slice = iconv_substr($str,$start,$length,$charset);
		}else{
			$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
			$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
			$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
			$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
			preg_match_all($re[$charset], $str, $match);
			$slice = join("",array_slice($match[0], $start, $length));
		}
		return $suffix ? $slice.'...' : $slice;
	}

	/**
	 +----------------------------------------------------------
	 * 产生随机字串，可用来自动生成密码
	 * 默认长度6位 字母和数字混合 支持中文
	 +----------------------------------------------------------
	 * @param string $len 长度
	 * @param string $type 字串类型
	 * 0 字母 1 数字 其它 混合
	 * @param string $addChars 额外字符
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	static public function randString($len=6,$type='',$addChars='') {
		$str ='';
		switch($type) {
			case 0:
				$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
				break;
			case 1:
				$chars= str_repeat('0123456789',3);
				break;
			case 2:
				$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
				break;
			case 3:
				$chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
				break;
			case 4:
				$chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借".$addChars;
				break;
			default :
				// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
				$chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
				break;
		}
		if($len>10 ) {//位数过长重复字符串一定次数
			$chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
		}
		if($type!=4) {
			$chars   =   str_shuffle($chars);
			$str     =   substr($chars,0,$len);
		}else{
			// 中文随机字
			for($i=0;$i<$len;$i++){
				$str.= self::msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1);
			}
		}
		return $str;
	}

	/**
	 +----------------------------------------------------------
	 * 生成一定数量的随机数，并且不重复
	 +----------------------------------------------------------
	 * @param integer $number 数量
	 * @param string $len 长度
	 * @param string $type 字串类型
	 * 0 字母 1 数字 其它 混合
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	static public function buildCountRand ($number,$length=4,$mode=1) {
		if($mode==1 && $length<strlen($number) ) {
			//不足以生成一定数量的不重复数字
			return false;
		}
		$rand   =  array();
		for($i=0; $i<$number; $i++) {
			$rand[] =   self::randString($length,$mode);
		}
		$unqiue = array_unique($rand);
		if(count($unqiue)==count($rand)) {
			return $rand;
		}
		$count   = count($rand)-count($unqiue);
		for($i=0; $i<$count*3; $i++) {
			$rand[] =   self::randString($length,$mode);
		}
		$rand = array_slice(array_unique ($rand),0,$number);
		return $rand;
	}

	/**
	 +----------------------------------------------------------
	 *  带格式生成随机字符 支持批量生成
	 *  但可能存在重复
	 +----------------------------------------------------------
	 * @param string $format 字符格式
	 *     # 表示数字 * 表示字母和数字 $ 表示字母
	 * @param integer $number 生成数量
	 +----------------------------------------------------------
	 * @return string | array
	 +----------------------------------------------------------
	 */
	static public function buildFormatRand($format,$number=1) {
		$str  =  array();
		$length =  strlen($format);
		for($j=0; $j<$number; $j++) {
			$strtemp   = '';
			for($i=0; $i<$length; $i++) {
				$char = substr($format,$i,1);
				switch($char){
					case "*"://字母和数字混合
						$strtemp   .= String::randString(1);
						break;
					case "#"://数字
						$strtemp  .= String::randString(1,1);
						break;
					case "$"://大写字母
						$strtemp .=  String::randString(1,2);
						break;
					default://其他格式均不转换
						$strtemp .=   $char;
						break;
				}
			}
			$str[] = $strtemp;
		}

		return $number==1? $strtemp : $str ;
	}

	/**
	 +----------------------------------------------------------
	 * 获取一定范围内的随机数字 位数不足补零
	 +----------------------------------------------------------
	 * @param integer $min 最小值
	 * @param integer $max 最大值
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	static public function randNumber ($min, $max) {
		return sprintf("%0".strlen($max)."d", mt_rand($min,$max));
	}
}