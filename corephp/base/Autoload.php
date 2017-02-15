<?php
/**
 * Created by PhpStorm.
 * User: shooke
 * Date: 17-2-15
 * Time: 上午9:58
 */

namespace corephp\base;


class Autoload
{
    /**
     * 载入路径
     * @var array
     */
    private $loadDir = [];

    /**
     * 初始化自动载入
     * @param array|string $path
     */
    public function __construct($path)
    {
        $this->addAutoLoadPath($path);
        $this->registerAutoload();
    }
    /**
     * 设置自动载入路径
     * @param array|string $path
     */
    public function addAutoLoadPath($path)
    {
        //如果是字符串则转换为数组
        if(is_string($path)){
            $path = [$path];
        }
        //合并载入路径的数组，并且去除重复值
        $this->loadDir = array_unique(array_merge($this->loadDir, $path));
    }

    /**
     * 注册自动加载
     */
    private function registerAutoload()
    {
        // 注册AUTOLOAD方法
        spl_autoload_register([$this,'loader']);
    }

    /**
     * 实现类的自动加载
     * @param string $className
     * @return boolean
     */
    private function loader($className)
    {
        $loadDir = $this->loadDir;
        foreach ($loadDir as $dir) {
            $file = $dir . DIRECTORY_SEPARATOR . $className . '.php';
            $file = str_replace(['//','\\\\','\\','/'], DIRECTORY_SEPARATOR, $file);//防止出现dir//dir或dir\\dir格式
            if (file_exists($file)) {
                require $file;
                return true;
            }
        }
        return false;
    }
}