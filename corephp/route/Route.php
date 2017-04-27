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
    /**
     * 中间件
     * @var array
     */
    public $middleware;

    /**
     * 默认要执行的控制器方法
     * @var string
     */
    public $defaultRun;
    /**
     * 控制器命名空间
     * @var string
     */
    public $controllerNamespace;

    /**
     * 路由规则
     * @var array
     */
    public $rules;
    
    public $routeParamName = '';


    /**
     * 获取请求方式
     * @return mixed
     */
    private function _method(){
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * 获取请求的路由
     * @return mixed
     */
    private function _uri()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    private function _runRule()
    {
        $uri = $this->_uri();
        $method = $this->_method();
        $route = $this->defaultRun;//控制器方法
        $arguments = [];//参数
        foreach ($this->rules as $rule) {
            //验证路由规则
            if (preg_match('#' . $rule['route'] . '#', $uri, $matches)) {
                //验证请求方式
                if (isset($rule['method']) && preg_match("/$method/i", $rule['method'])) {
                    $route = $rule['run'];
                    array_shift($matches);//移除完整匹配保留参数
                    $arguments = $matches;
                    break;
                }
            }
        }
        return [
            'route'=>$route,
            'arguments'=>$arguments
        ];
    }
    /**
     * Runs the callback for the given request
     */
    public function dispatch()
    {
        if($this->rules){
            $call = $this->_runRule();
        }else{

        }

        $className = preg_replace('/\\+|\/+/', '\\', $this->controllerNamespace.'/'.dirname($call['route']));
        $controllerObject = new $className;
        $action = basename($call['route']);
        return call_user_func_array([$controllerObject,$action],$call['arguments']);


    }

}