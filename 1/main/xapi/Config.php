<?php

// cookie中使用的hostname
define('HOST_NAME', ROOT_DOMAIN);

define('REQUEST_TIMERANGE', 300);

define('USER_LOGIN_TIMEOUT', 2592000);

define('GATEWAY_KEY', 'n30ky31wn2');
define('GATEWAY_SECRET', 'j3jy2x05xk44iy2ih3yhw33330zx451ii024kl34');

define('WEB_APP_KEY', '4o1jn0j30z');

define('ADMIN_APP_KEY', 'j31xol234l');

//SAE Storage for main
define('MAIN_STORAGE', 'main');
define('MAIN_STORAGE_URL', 'http://developapi-main.stor.sinaapp.com/');

//SAE Storage for private
define('PRIVATE_STORAGE', 'private');
define('PRIVATE_STORAGE_URL', 'http://developapi-private.stor.sinaapp.com/');

//baidu OAUTH
define('BAIDU_OAUTH_URL', 'https://openapi.baidu.com/oauth/2.0/token');
define('BAIDU_GRANT_TYPE', 'client_credentials');
define('BAIDU_CLIENT_ID', 'SqgHW8HtcswUcpAw6RCXqGod');
define('BAIDU_CLIENT_SECRET', 'fa28a20d3b4d9e7c30be9987df1d3aa5');

//baidu tts
define('BAIDU_TTS_URL', 'http://tsn.baidu.com/text2audio');

//发送短信
define('BECH_SMS_ACCESS_KEY', '4676');
define('BECH_SMS_SECRET_KEY', 'b6f99512208bd218b091c9d8c7c61fe9af72caea');

define('MESSAGE_SMS_URL', 'http://imlaixin.cn/Api/send/data/json?accesskey=%s&secretkey=%s');

define('TEMPLATE_SMS', '验证码：%s，请于10分钟内输入，切勿告知他人。【方向教育】');

// Mysql配置 (host, user, passwd, db_name, port)
$g_mysql_masters = array(
    array(SAE_MYSQL_HOST_M, SAE_MYSQL_USER, SAE_MYSQL_PASS, SAE_MYSQL_DB, SAE_MYSQL_PORT, 0),
);
$g_mysql_slaves = array(
    array(SAE_MYSQL_HOST_S, SAE_MYSQL_USER, SAE_MYSQL_PASS, SAE_MYSQL_DB, SAE_MYSQL_PORT, 0),
);
