<?php
/**
 * ControllerBase是一个Controller基类，约定Controller的相关操作
 * 其他子类可以通过继承该类来进行扩展
 */

abstract class ControllerBase {
  /**
   * 默认的Controller入口函数
   * 不建议重载这个函数
   */
  public function Execute() {
    try {
      $this->Setup();
      $this->Run();
      $this->TearDown();
    } catch (Exception $e) {
    }
  }

  /**
   * 默认的初始化入口函数
   * 可以重载这个函数，在你需要的时候
   */
  public function Setup() {
    
  }

  /**
   * 默认的主控入口函数
   * 需要重载这个函数，加入子类的控制逻辑
   */
  abstract public function Run();

  /**
   * 统一的错误处理逻辑
   *
   */
  protected function SeeError($response) {}

  /**
   * 默认的销毁入口函数
   * 可以重载这个函数，在你需要的时候
   */
  public function TearDown() {
    
  }
}
