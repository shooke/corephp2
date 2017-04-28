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
    const ROUTE_TYPE_PATHINFO = 'PATHINFO';
    const ROUTE_TYPE_PARAM = 'param';
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
     * [
     * ['route'=>'view/(.*)','run'=>'class/action','method'=>'get|post']
     * ]
     * @var array
     */
    public $rules;
    /**
     * 是否隐藏执行文件
     * @var bool
     */
    public $hiddenScriptFile = true;
    /**
     * 路由形式
     * pathinfo 或 param
     * @var string
     */
    public $routeType = 'pathinfo';
    /**
     * 路由接收参数
     * @var string
     */
    public $routeParamName = 'r';


    /**
     * 获取请求方式
     * @return mixed
     */
    private function _method(){
        return $_SERVER['REQUEST_METHOD'];
    }


    /**
     * 根据规则解析路由
     * @return array
     */
    private function _parseRule()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
        $method = $this->_method();
        $route = '';//控制器方法
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
     * 普通形式解析路由
     * @return array
     */
    private function _parseUrlString()
    {
        isset($_SERVER['QUERY_STRING']) ? parse_str($_SERVER['QUERY_STRING'],$paramArray) : parse_str(parse_url($_SERVER['REQUEST_URI'],PHP_URL_QUERY),$paramArray);
        $call = [];
        if(isset($paramArray[$this->routeParamName])){
            $call['route'] = $paramArray[$this->routeParamName];
            unset($paramArray[$this->routeParamName]);
            $call['arguments'] = $paramArray;
        }else{
            $call = [
                'route'=>'',
                'arguments'=>$paramArray
            ];
        }

        return $call;
    }

    /**
     * pathinfo形式解析路由
     * @return array
     */
    private function _parsePathinfo()
    {
        if(isset($_SERVER['PATH_INFO'])){
            $route = $_SERVER['PATH_INFO'];
        }else{
            $route = str_replace($_SERVER['SCRIPT_NAME'],'',parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        }

        isset($_SERVER['QUERY_STRING']) ? parse_str($_SERVER['QUERY_STRING'],$paramArray) : parse_str(parse_url($_SERVER['REQUEST_URI'],PHP_URL_QUERY),$paramArray);
        return [
            'route'=>$route,
            'arguments'=>$paramArray
        ];
    }


    /**
     * 执行中间件和控制器
     * @param $call
     * @return mixed
     */
    private function _execute($call)
    {
        $className = preg_replace('/\\+|\/+/', '\\', $this->controllerNamespace.'/'.dirname($call['route']));
        $controllerObject = new $className;
        $action = basename($call['route']);
        //执行中间件前置操作
        $this->middleware->before();
        //执行控制器方法
        $result = call_user_func_array([$controllerObject,$action],$call['arguments']);
        //执行中间件后置操作
        $this->middleware->after();

        return $result;
    }

    /**
     * 执行运行
     */
    public function run()
    {
        //根据规则操作路由
        if($this->rules){
            $call = $this->_parseRule();
            if($call['route']){
                return $this->_execute($call);
            }
        }

        //解析规则外的规则
        switch (strtoupper($this->routeType)){
            case self::ROUTE_TYPE_PATHINFO:
                $call = $this->_parsePathinfo();
                break;
            case self::ROUTE_TYPE_PARAM:
                $call = $this->_parseUrlString();
        }

        //如果路由为空或为斜杠（/）则使用默认路由
        if(empty($call['route']) || $call['route'] == '/'){
            $call['route'] = $this->defaultRun;
        }

        return $this->_execute($call);


    }

    /**
     * 生成url
     * @param $ctrlAction
     * @param array $param
     * @param string $anchor
     * @return string
     */
    public function createUrl($ctrlAction,$param=[],$anchor='')
    {
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $route = '';
        $url = '';
        if($this->rules){
            foreach ($this->rules as $rule){
                if($rule['run']=$ctrlAction){
                    $route = $rule['route'];
                    break;
                }
            }
            if($route){

            }
        }
        if($route==''){
            $route = $ctrlAction;
            //解析规则外的规则
            switch (strtoupper($this->routeType)){
                case self::ROUTE_TYPE_PATHINFO:
                    $url .= $this->hiddenScriptFile ? dirname($scriptName).'/'.$route : $scriptName.'/'.$route;
                    $url .= '?'.http_build_query($param);
                    break;
                case self::ROUTE_TYPE_PARAM:
                    $urlParam = [$this->routeParamName=>$route];
                    if($param){
                        foreach ($param as $key=>$value){
                            $urlParam[$key] = $value;
                        }
                    }
                    $url .= $this->hiddenScriptFile
                        ? dirname($scriptName).'?'.http_build_query($urlParam)
                        : $scriptName.'?'.http_build_query($urlParam);

            }
        }
        $url .= $anchor ? '#'.$anchor : '';
        return $url;

    }

}