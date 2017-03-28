<?php
/**
 * Created by PhpStorm.
 * User: shooke
 * Date: 17-3-28
 * Time: 上午10:10
 */

namespace corephp\base;


use corephp\exception\TypeErrorException;

class Object implements Root
{
    public $computed;
    protected $computedObject;
    public $watch;
    public $attributes=[];

    public function __get($name)
    {
        if(isset($this->$name)) return $this->$name;
        if(isset($this->attributes[$name])) return $this->attributes[$name];
        if($this->computed){
            if(!is_object($this->computedObject)){
                $computed = new $this->computed();
                if($computed instanceof Computed){
                    $computed->caller = $this;
                    $this->computedObject = $computed;
                }else{
                    throw new TypeErrorException('计算属性类未继承 corephp\base\Computed');
                }
            }
            return call_user_func([$this->computedObject,$name]);
        }
        return null;
    }
    public function __set($name ,$value)
    {
        if(isset($this->$name)) $this->$name = $value;
        if(isset($this->attributes[$name])) $this->attributes[$name] = $value;
    }
}