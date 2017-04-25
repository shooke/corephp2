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
    private $level;
    public function error($file,$message)
    {
        $this->level = self::ERROR;
        $this->_write($file,$this->_content($message));

    }

    public function warning($file,$message)
    {
        $this->level = self::WARNING;
        $this->_write($file,$this->_content($message));
    }

    public function notice($file,$message)
    {
        $this->level = self::NOTICE;
        $this->_write($file,$this->_content($message));
    }

    public function info($file,$message)
    {
        $this->level = self::INFO;
        $this->_write($file,$this->_content($message));
    }

    /**
     * 处理格式
     * @param $message
     * @return mixed
     */
    private function _content($message)
    {
        $url = $_SERVER['REQUEST_URI']?$_SERVER['REQUEST_URI']:($_SERVER['PHP_SELF']?$_SERVER['PHP_SELF']:$_SERVER['SCRIPT_NAME']);
        $log = date('Y-m-d H:i:s')." [{$url}] [{$this->level}]". var_export($message,true);
        return $log;
    }

    /**
     * 写入文件
     * @param $file
     * @param $message
     */
    private function _write($file,$message)
    {
        if(!is_dir($this->logPath)){
            mkdir($this->logPath,0777,true);
        }
        if(is_writable($this->logPath)){
            chmod($this->logPath,0777);
        }
        file_put_contents($file, $message, FILE_APPEND | LOCK_EX);
        if($this->halt){
            exit;
        }
    }

}