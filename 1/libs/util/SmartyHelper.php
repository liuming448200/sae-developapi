<?php
/**
 * Smarty公共类的封装
 */

require PHP_ROOT . 'libs/thirdparty/Smarty/libs/Smarty.class.php';

class SmartyHelper {
  const LEFT_DELIMITER = '{%';
  const RIGHT_DELIMITER = '%}';
  private static $tpl_path_;
  private static $tpc_path_;
  private static $sconf_path_;
  private static $scache_path_;
  private static $splugin_path_;

  /**
   * Set smarty path parameters
   * Called by index.php
   * @param $tpl_path
   * @param $tpc_path
   * @param $sconf_path
   * @param $scache_path
   * @param $splugin_path
   */
  public static function SetSmartyConfig($smarty_config_list) {
    self::$tpl_path_ = $smarty_config_list['tpl_path'];
    self::$tpc_path_ = $smarty_config_list['tpc_path'];
    self::$sconf_path_ = $smarty_config_list['sconf_path'];
    self::$scache_path_ = $smarty_config_list['scache_path'];
    self::$splugin_path_ = $smarty_config_list['splugin_path'];
  }

  /**
   * Get a smarty object and init it's path
   */
  public static function GetSmartyObj() {
    $smarty = new Smarty();
    $smarty->template_dir = self::$tpl_path_;
    $smarty->compile_dir = self::$tpc_path_;
    $smarty->config_dir = self::$sconf_path_;
    $smarty->cache_dir = self::$scache_path_;
    $smarty->plugins_dir = self::$splugin_path_;
    $smarty->compile_locking = false;
    $smarty->left_delimiter  = self::LEFT_DELIMITER;
    $smarty->right_delimiter = self::RIGHT_DELIMITER;
    return $smarty;
  }
}
