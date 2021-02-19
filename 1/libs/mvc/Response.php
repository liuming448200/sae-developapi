<?php
/**
 * Model 中统一的响应数据
 *
 */
class Response {
  const SUCCESS = 200;

  public $status = self::SUCCESS;
  public $message = '';
  public $data = array();
}
