<?php
/**
 * Created by PhpStorm.
 * User: shooke
 * Date: 17-4-24
 * Time: 下午4:01
 */

namespace corephp\log;


class File extends LogAbstract
{
    public $logPath;
    public $halt = false;
    /**
     * 处理格式
     * @param $message
     * @param $level
     * @return string
     */
    private function _content($message,$level)
    {
        $url = $_SERVER['REQUEST_URI']?$_SERVER['REQUEST_URI']:($_SERVER['PHP_SELF']?$_SERVER['PHP_SELF']:$_SERVER['SCRIPT_NAME']);
        $log = date('Y-m-d H:i:s')." [{$url}] [{$level}]". var_export($message,true);
        return $log;
    }

    /**
     * 写入文件
     * @param $file
     * @param $message
     * @param $level
     */
    protected function save($file,$message,$level)
    {
        if(!is_dir($this->logPath)){
            mkdir($this->logPath,0777,true);
        }
        if(is_writable($this->logPath)){
            chmod($this->logPath,0777);
        }
        $message = $this->_content($message,$level);
        file_put_contents($file, $message, FILE_APPEND | LOCK_EX);
        if($this->halt){
            exit;
        }
    }

}