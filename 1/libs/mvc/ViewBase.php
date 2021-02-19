<?php
/**
 * ViewBase是一个View基类，约定View的相关操作
 * 其他子类可以通过继承该类来进行扩展
 */
abstract class ViewBase {
  /**
   * 展示View页面，需要子类去重载该函数后自己去实现
   */
  abstract public function Display();
}
