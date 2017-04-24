<?php
/**
 * Created by PhpStorm.
 * User: shooke
 * Date: 17-4-24
 * Time: 下午4:03
 */

namespace corephp\log;


interface LogInterface
{
    public function emergency();
    public function alert();
    public function critical();
    public function error();
    public function warning();
    public function notice();
    public function info();
    public function debug();

}