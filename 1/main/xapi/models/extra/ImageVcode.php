<?php

/**
 * 图片验证码.
 */

require (WEB_ROOT . 'models/extra/Image.php');

class ImageVcode {

    const MEMCACHE_GROUP = 'default';

    const MC_KEY_PRE = 'Image_Verify_Code_Key_';
    const MC_KEY_VERIFY_COUNT_PRE = 'Image_Verify_Code_Count_';

    /**
     * 取图片验证码.
     */
    public static function getImageVerifyCode ($identity, $type='gif') {
        Image::buildImageVerify(5, 1, $type);
        $vcode = Image::getVcode();

        $mcHandler = MemcachedClient::GetInstance(self::MEMCACHE_GROUP);
        $mcHandler->set(self::MC_KEY_PRE.$identity, $vcode, 60*5);
        $mcHandler->set(self::MC_KEY_VERIFY_COUNT_PRE.$identity, 0, 60*5); //计数器
    }

    /**
     * 验证图片验证码.
     */
    public static function checkImageVerifyCode ($identity, $vcode) {
        $st = false;
        
        $mcHandler = MemcachedClient::GetInstance(self::MEMCACHE_GROUP);
        $val = $mcHandler->get(self::MC_KEY_PRE.$identity);
        $count = $mcHandler->increment(self::MC_KEY_VERIFY_COUNT_PRE.$identity);
        if ($val && $count <= 5){ //二维码存在&&验证次数小于5次
            $st = $val==md5($vcode)? true: false;
            if ($st){
                $mcHandler->delete(self::MC_KEY_PRE.$identity);
            }
        } else {
            $mcHandler->delete(self::MC_KEY_PRE.$identity);
        }

        return $st;
    }
}