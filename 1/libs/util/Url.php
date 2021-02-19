<?php
/**
 * Url常用处理函数
 */

require PHP_ROOT . 'libs/util/HttpBuildUrl.php';

class Url {
  const kQuery = 'query';
  // 这是url解析后的数组，包含如下若干key值
  // 'scheme'，'host'，'port'，'user'，'pass'，'path'，'query'，'fragment'
  private $url_component_ = array();
  // 这是url参数解析后的数组
  private $url_parameter_list_ = array();

  public function __construct($url) {
    $this->url_component_ = parse_url($url);
    // Logg::Debug($url);
    if (isset($this->url_component_[self::kQuery])) {
      parse_str($this->url_component_[self::kQuery], $this->url_parameter_list_);
    }
  }

  public function SetParameter($key, $value) {
    $this->url_parameter_list_[$key] = $value;
  }

  public function GetParameter($key) {
    return @$this->url_parameter_list_[$key];
  }

  public function RemoveParameter($key) {
    unset($this->url_parameter_list_[$key]);
  }

  public function GetUrl() {
    // http_build_query函数返回的url字符串已经encoded过了
    $this->url_component_[self::kQuery] = http_build_query($this->url_parameter_list_);
    return http_build_url(NULL, $this->url_component_);
  }
}
