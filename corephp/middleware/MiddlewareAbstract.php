<?php
/**
 * Created by PhpStorm.
 * User: shooke
 * Date: 17-4-19
 * Time: 下午3:12
 */

namespace corephp\middleware;

/**
 * 中间件抽象类
 * 所有中间件都必须继承此类，并实现其中的 before 和 after 方法
 * Class MiddlewareAbstract
 * @package corephp\middleware
 */
abstract class MiddlewareAbstract
{
    /**
     * 启用中间件的控制器
     * @return mixed
     */
    public function enableClass()
    {

    }

    /**
     * 禁用中间件的控制器
     * @return mixed
     */
    public function disableClass()
    {

    }

    /**
     * 启用中间件的方法
     * @return mixed
     */
    public function enableAction()
    {

    }

    /**
     * 禁用中间件的方法
     * @return mixed
     */
    public function disableAction()
    {

    }

    /**
     * 控制器方法执行前执行
     * @return mixed
     */
    abstract protected function before();

    /**
     * 控制器方法执行后执行
     * @return mixed
     */
    abstract protected function after();
}