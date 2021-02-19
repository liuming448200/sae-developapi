<?php

require PHP_ROOT . 'libs/mvc/HttpViewBase.php';

class SmartyView extends HttpViewBase {
  protected $data_;
  protected $template_name_;

  public function SetData($data) {
    $this->data_ = $data;
  }

  public function SetTemplate($template_name) {
    $this->template_name_ = $template_name;
  }

  public function Display() {
    // 渲染页面
    $smarty = SmartyHelper::GetSmartyObj();
    $smarty->assign('data', $this->data_);
    $smarty->display($this->template_name_);
  }
}
