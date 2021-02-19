<?php
/**
 *  时间差工具类
 * 
 **/
class RunTimeUtil 
{
    private $_startTime = 0;
    private $_stopTime = 0;
  
    public function __construct() {
        $this->start();
    }

    public function get_microtime()
    {
        list($usec, $sec) = explode(' ', microtime());
        return (float)$usec + (float)$sec;
    }

    public function start()
    {
        $this->_startTime= $this->get_microtime();
    }

    public function stop()
    {
        $this->_stopTime = $this->get_microtime();
    }

    //返回时间差  ms
    public function spent()
    {
        $this->stop();
        return round(($this->_stopTime - $this->_startTime) * 1000, 1);
    }

}
