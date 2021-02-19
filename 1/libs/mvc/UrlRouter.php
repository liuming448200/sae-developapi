<?php
/**
 * 根据配置的路由表进行Url路由寻址，遍历路由表，按照最长匹配的原则进行
 * 在路由表的设置中，相同前缀的路由规则，请注意路由规则的长度，它直接影响路由的结果
 */


class UrlRouter {
  protected $mapping_ = array();
  protected $controller_path_;
  private $antispam_mapping_ = array();

  public function SetMapping($mapping) {
    $this->mapping_ = $mapping;
  }

  public function SetAntispamMapping($antispam_mapping) {
    $this->antispam_mapping_ = $antispam_mapping;
  }

  public function SetControllerPath($controller_path) {
    $this->controller_path_ = $controller_path;
  }

  public function Route() {
    if (!$this->controller_path_) return;
    // 兼容web服务器的rewrite路由
    $uri = isset($_SERVER['PATH_INFO']) ? str_replace('index.php', '', $_SERVER['PATH_INFO']) : $_SERVER['REQUEST_URI'];
    $matched_uri = $this->Match($uri);
    if ($matched_uri) {
      $this->AntispamFilter($matched_uri);
      $controller_name = $this->mapping_[$matched_uri];
    } else {
      $controller_name = $this->GetNotFoundController();
    }
    require $this->controller_path_.'/'.$controller_name.'.php';
    $controller_name = basename($controller_name); // controller支持多目录层级
    $controller = new $controller_name();
    $controller->Execute();
  }

  public function AntispamFilter($uri) {
    if ($this->antispam_mapping_ && isset($this->antispam_mapping_[$uri])) {
      require PHP_ROOT . 'libs/util/AntispamUtility.php';
      AntispamUtility::Filter($this->antispam_mapping_[$uri]);
    }
    return true;
  }

  public function Match($uri) {
    // 从前到后寻找uri中第一个'?'出现的位置
    $position = strpos($uri, '?');
    if ($position) $uri = substr($uri, 0, $position);
    while (TRUE) {
      if (isset($this->mapping_[$uri])) {
        return $uri;
      }
      // 从后到前寻找uri中第一个'/'出现的位置
      $position = strrpos($uri, '/');
      if ($position)
        $uri = substr($uri, 0, $position);
      else
        break;
    }
    // 未找到匹配值
    return false;
  }

  // maping_中找不到对应值时，指定默认处理controller:
  //   'libs/mvc/NotFoundController.php'
  //    默认直接302跳转到首页
  // 需子类重写，自定义controller
  protected function GetNotFoundController() {
    $this->controller_path_ = PHP_ROOT . 'libs/mvc';
    return 'NotFoundController';
  }
}

