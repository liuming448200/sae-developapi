<?php

/**
 * 多域名支持，初始化当前根域名
 */
$_root_domains = array('developapi.applinzi.com', 'api.directionedu.com');
foreach ($_root_domains as $_root_domain) {
  if (stripos($_SERVER['HTTP_HOST'], $_root_domain)) {
    define('ROOT_DOMAIN', $_root_domain);
    break;
  }
}

// 开发环境指定一个root domain
if (!defined('ROOT_DOMAIN')) {
    define('ROOT_DOMAIN', $_root_domains[0]);
}
