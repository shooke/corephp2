<?php
/**
 * Created by PhpStorm.
 * User: shooke
 * Date: 17-4-25
 * Time: 上午10:14
 */

namespace corephp\log;


abstract class LogAbstract
{
    const INFO = 'info';
    const NOTICE = 'notice';
    const WARNING = 'warning';
    const ERROR = 'error';
    const CRITICAL = 'critical';
    const ALERT = 'alert';
    const EMERGENCY = 'emergency';
    const DEBUG = 'debug';
    /**
     * 仅是一些基本的讯息说明而已
     * @param $message
     * @param $file
     */
    public function info($file, $message)
    {
        $this->save($file,$message,self::INFO);
    }

    /**
     * 比 info 还需要被注意到的一些信息内容
     * @param $message
     * @param $file
     */
    public function notice($file, $message)
    {
        $this->save($file,$message,self::NOTICE);
    }

    /**
     * 警示讯息，可能有问题，但是还不至于影响到某个 daemon 运作
     * @param $message
     * @param $file
     */
    public function warning($file, $message)
    {
        $this->save($file,$message,self::WARNING);
    }

    /**
     * 一些重大的错误讯息，这就要去找原因了
     * @param $file
     * @param $message
     */
    public function error($file, $message)
    {
        $this->save($file,$message,self::ERROR);
    }

    /**
     * 比 error 还要严重的错误信息，crit 是临界点 (critical) 的缩写，已经很严重了！
     * @param $message
     * @param $file
     */
    public function critical($file, $message)
    {
        $this->save($file,$message,self::CRITICAL);
    }

    /**
     * 警告警告，已经很有问题的等级，比 crit 还要严重！
     * @param $message
     * @param $file
     */
    public function alert($file, $message)
    {
        $this->save($file,$message,self::ALERT);
    }

    /**
     * 疼痛等级，意指系统已经几乎要当机的状态！ 很严重的错误信息了
     * @param $message
     * @param $file
     */
    public function emergency($file, $message)
    {
        $this->save($file,$message,self::EMERGENCY);
    }

    /**
     * 错误侦测等级
     * @param $message
     * @param $file
     */
    public function debug($file, $message)
    {
        $this->save($file,$message,self::DEBUG);
    }

    /**
     * 保存日志
     * @param $file
     * @param $message
     * @param $level
     */
    abstract protected function save($file, $message,$level);
}