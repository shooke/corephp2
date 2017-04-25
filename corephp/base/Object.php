<?php
/**
 * Created by PhpStorm.
 * User: shooke
 * Date: 17-3-28
 * Time: 上午10:10
 */

namespace corephp\base;

class Object implements Root
{
    /**
     * 调用读取方法
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
    }

    /**
     * 调用设置方法
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        }
    }
}