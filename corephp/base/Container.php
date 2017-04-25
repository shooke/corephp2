<?php
/**
 * Created by PhpStorm.
 * User: shooke
 * Date: 17-3-31
 * Time: 上午11:39
 */

namespace corephp\base;

/**
 * 容器
 * 先set所有的依赖和要初始化的类，然后调用get取出
 * try{
 * $container = new Container;
 *     $container->set('a');
 *     var_dump($container->get('a'));
 *      $container->set('namespace\a',['param1'],'ns\a');
 * }catch(Exception $e){
 *     echo $e->getMessage();
 * }
 * @package corephp\base
 */
class Container
{
    // 用于保存已生成的实例
    private $_objects = [];

    // 用于保存依赖的定义，以对象类型为键
    private $_classes = [];

    // 用于保存构造函数的参数，以对象类型为键
    private $_params = [];

    private $_dependencies = [];

    // 要实例化的类名
    private $_buildingClass;

    /**
     * 设置依赖关系
     * class为要实例化的类
     * params为构造函数所需参数
     * dependencies为构造函数中参数的限定类型
     * 如构造函数所需类型db::__construct(ns\db $db)
     * $container->set('ns\myssql',['param1'],'ns\db');
     * $container->set('db',[
     * 'dsn'=>'',
     * ]);
     * $container->get('db');
     * 程序会去实例化ns\mysql作为参数传入
     * @param $class
     * @param array $params
     * @param string $dependencies
     * @return $this
     */
    public function set($class, array $params = [],$dependencies='')
    {
        $this->_classes[$class] = $class;
        $this->_params[$class] = $params;
        if($dependencies){
            $this->_dependencies[$dependencies] = $class;
        }
        unset($this->_objects[$class]);
        return $this;
    }

    public function get($class)
    {
        if (isset($this->_objects[$class])) {
            return $this->_objects[$class];
        } else {
            $this->_objects[$class] = $this->build($class);
            return $this->_objects[$class];
        }
    }


    /**
     * 自动绑定（Autowiring）自动解析（Automatic Resolution）
     *
     * @param string $className
     * @return object
     * @throws Exception
     */
    public function build($className)
    {
        // 如果是匿名函数（Anonymous functions），也叫闭包函数（closures）
        if ($className instanceof Closure) {
            // 执行闭包函数，并将结果
            return $className($this);
        }
        $this->_buildingClass = $className;
        /**
         * @var ReflectionClass $reflector
         */
        $reflector = new ReflectionClass($className);

        // 检查类是否可实例化, 排除抽象类abstract和对象接口interface
        if (!$reflector->isInstantiable()) {
            throw new Exception("Can't instantiate this.");
        }

        /**
         * 获取类的构造函数
         * @var ReflectionMethod $constructor
         */
        $constructor = $reflector->getConstructor();

        // 若无构造函数，直接实例化并返回
        if (is_null($constructor)) {
            return new $className;
        }

        /**
         * 取构造函数参数,通过 ReflectionParameter 数组返回参数列表
         * @var ReflectionParameter $parameters
         */
        $parameters = $constructor->getParameters();

        /**
         * 解析构造函数的参数
         * 如果传入了参数则直接使用用户传入的参数，如果没有设置，则自动解析
         */
        $dependencies = $this->_params[$className] ? $this->_params[$className] : $this->getDependencies($parameters);

        // 创建一个类的新实例，给出的参数将传递到类的构造函数。
        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * @param array $parameters
     * @return array
     * @throws Exception
     */
    public function getDependencies($parameters)
    {
        $dependencies = [];

        /**
         * @var ReflectionParameter $parameter
         */
        foreach ($parameters as $parameter) {
            /**
             * @var ReflectionClass $dependency
             */
            $dependency = $parameter->getClass();

            if (is_null($dependency)) {
                // 是变量,有默认值则设置默认值
                $dependencies[] = $this->resolveNonClass($parameter);
            } else {
                // 声明过对应依赖则调用对应依赖，未声明则直接根据类型创建
                $dependenciesClass = isset($this->_dependencies[$dependency->name]) ? $this->_dependencies[$dependency->name] : $dependency->name;
                $dependencies[] = $this->build($dependenciesClass);
            }
        }

        return $dependencies;
    }

    /**
     * @param ReflectionParameter $parameter
     * @return mixed
     * @throws Exception
     */
    public function resolveNonClass($parameter)
    {
        // 有默认值则返回默认值
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }
        throw new Exception($this->_buildingClass . "::__construct() 缺少参数");
    }
}