<?php
/**
 * Model基类，规范化基于Response[Class]的数据统一获取接口
 *
 */


require(PHP_ROOT . 'libs/mvc/Response.php');

abstract class ModelBase {

  /**
   * 主流程，重写以添加登陆判断、权限判断、错误处理等。
   */
  public function GetResponse() {
    return $this->GetResponse_();
  }

  /**
   * 主逻辑
   * 必须返回Response或者其子类
   */
  abstract protected function GetResponse_();

}
