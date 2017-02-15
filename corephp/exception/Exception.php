<?php
/**
 * Created by PhpStorm.
 * User: shooke
 * Date: 17-2-15
 * Time: 上午10:31
 */

namespace corephp\exception;



class Exception
{
    /**
     * 初始化处理
     */
    public function register()
    {
        ini_set('display_errors', false);
        set_exception_handler([$this, 'handleException']);
        set_error_handler([$this, 'handleError']);
        register_shutdown_function([$this, 'handleFatalError']);
    }

    /**
     * 恢复默认处理
     */
    public function unregister()
    {
        restore_error_handler();
        restore_exception_handler();
    }

    /**
     * 异常处理
     * @param \Exception $exception
     */
    public function handleException($exception)
    {
        //恢复默认处理
        $this->unregister();

        // 通知服务器，500代码错误
        http_response_code(500);

        echo $exception->getMessage().PHP_EOL.$exception->getTraceAsString();

    }

    /**
     * 错误处理返回异常
     * @param $code
     * @param $message
     * @param $file
     * @param $line
     * @throws \ErrorException
     */
    public function handleError($code, $message, $file, $line)
    {
        throw new \ErrorException($message, $code, $code, $file, $line);
    }

    /**
     * 发生错误返回异常
     * @throws \ErrorException
     */
    public function handleFatalError()
    {
        $error = error_get_last();
        throw new \ErrorException($error['message'], $error['type'], $error['type'], $error['file'], $error['line']);
    }
}