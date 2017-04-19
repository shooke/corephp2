<?php
/**
 * Created by PhpStorm.
 * User: shooke
 * Date: 17-2-15
 * Time: 上午10:30
 */

namespace corephp\route;


class Route
{
    private $rules;
    public function __construct($rules)
    {
        $this->$rules = $rules;
        $this->_initRule();
    }

    public function parse()
    {

    }
    public function createUrlByRoute()
    {

    }
    public function createUrlByController()
    {

    }
    public function runAction()
    {

    }

    private function _initRule()
    {

    }
}