<?php
/**
 * Created by PhpStorm.
 * User: shooke
 * Date: 17-4-25
 * Time: 下午4:03
 */

namespace corephp\base;


class Factory
{
    /**
     * @var Container
     */
    public static $container;
    /**
     * @var array
     */
    public static $singleObject = [];

    /**
     * 创建实例
     * $params = [
     *     'class'=>要实例化的类名,
     *     'property'=>对象的属性,
     *     'param'=>构造函数需要的参数
     * ]
     * @param $params
     * @return $this
     */
    public static function createObject($params)
    {
        $object = self::container()->set($params['class'],$params['param']);
        if(isset($params['property'])){
            foreach ($params['property'] as $name=>$value){
                $object->$name = $value;
            }
        }
        return $object;
    }

    /**
     * 单例模式实例化
     * @param $params
     * @return mixed
     */
    public static function singleObject($params)
    {
        if(!isset(self::$singleObject[$params['class']])){
            self::$singleObject[$params['class']] = self::createObject($params);
        }
        return self::$singleObject[$params['class']];
    }

    /**
     * 单例模式取得容器实例
     * @return Container
     */
    public static function container()
    {
        if(is_null(self::$container)){
            self::$container = new Container();
        }
        return self::$container;
    }

}