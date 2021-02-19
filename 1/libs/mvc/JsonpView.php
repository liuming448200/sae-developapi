<?php
/**
 * JsonpView是一个View类，
 * 在GET参数中指定了callback字段的时候，返回Jsonp。其他情况正常返回Json。
 * 输出Json格式的http响应
 */

require PHP_ROOT . 'libs/mvc/HttpViewBase.php';

class JsonpView extends HttpViewBase {
  protected $data_;
  //是否压缩返回结果
  protected $compress = false;
  public function __construct() {
  }

  public function SetData(&$data) {
    $this->data_ = $data;
  }

  public function Display() {
    $callback_name = @$_GET['callback'];
    if(!empty($callback_name)) {
      $result = json_encode($this->data_);
      $this->SetHeader("Content-Type: text/javascript; charset=UTF-8");
      $result = "window." . $callback_name . " && " . $callback_name . "(" . $result . ")";
    }
    else {
      $result = json_encode($this->data_);
      $this->SetHeader("Content-Type: application/json; charset=UTF-8");
      $result = $this->doCompress($result);
    }
    echo $result;
  }

  public function toCompress()
  {
    $this->compress = true; 
  }

  protected function doCompress($result)
  {
      if($this->compress && !headers_sent() && extension_loaded("zlib") && isset($_SERVER["HTTP_ACCEPT_ENCODING"]) && strstr($_SERVER["HTTP_ACCEPT_ENCODING"],"gzip"))
      {
          $result = gzencode($result,9);
          $this->SetHeader("Content-Encoding: gzip");
          $this->SetHeader("Vary: Accept-Encoding");
          $this->SetHeader("Content-Length: ".strlen($result));
      }
      return $result;
  }
}
