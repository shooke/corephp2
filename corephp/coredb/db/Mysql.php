<?php
/**
 * Created by PhpStorm.
 * User: shooke
 * Date: 17-2-15
 * Time: 上午10:24
 */

namespace corephp\coredb\db;

use PDO;
use PDOException;

class Mysql extends DbAbstract
{
    /**
     * 数据库链接相关属性
     */
    public $dsn = 'mysql:dbname=testdb;host=127.0.0.1';
    public $username = 'dbuser';
    public $password = 'dbpassword';
    public $options = [
        PDO::ATTR_PERSISTENT   => false,//长连接 true使用 false不使用
        PDO::ATTR_ERRMODE      => PDO::ERRMODE_EXCEPTION,// 设置抛出错误
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,// 指定数据库返回的NULL值在php中对应的数值 不变
        PDO::ATTR_CASE         => PDO::CASE_NATURAL,// 强制PDO 获取的表字段字符的大小写转换,原样使用列值
    ];
    public $charset = 'utf8';

    /**
     * @var null|PDO
     */
    protected $pdo = null;
    /**
     * @var array
     */
    protected $logs = [];

    protected $lastInsertId;
    protected $rowCount;

    /**
     * 记录sql执行
     * @param $sql
     * @param $params
     */
    private function saveLogs($sql,$params)
    {
        $this->logs[] = [
            'sql'=>$sql,
            'params'=>$params,
        ];
    }
    /**
     * 根据数据库配置创建或返回连接
     * @return PDO|null
     * @throws PDOException
     */
    public function pdo()
    {
        if (!is_null($this->pdo)) {
            return $this->pdo;
        }

        try {
            $commands = [
                'SET SQL_MODE=ANSI_QUOTES',
                "SET NAMES '{$this->charset}'"
            ];

            //建立链接
            $this->pdo = new PDO(
                $this->dsn,
                $this->username,
                $this->password,
                $this->options
            );

            foreach ($commands as $cmd) {
                $this->pdo->exec($cmd);
            }
            return $this->pdo;
        } catch (PDOException $pdoException) {
            throw $pdoException;
        }
    }

    /**
     * 执行插入
     * @param $sql
     * @param array $params
     * @return bool
     */
    public function insert($sql, $params = [])
    {
        $this->saveLogs($sql,$params);

        $this->rowCount = 0;
        $this->lastInsertId = 0;

        $statement = $this->pdo()->prepare($sql);
        $result = $statement->execute($params);
        $this->rowCount = $statement->rowCount();
        $this->lastInsertId = $this->pdo()->lastInsertId();
        return $result;
    }

    /**
     * 执行替换式插入
     * @param $sql
     * @param array $params
     * @return bool
     */
    public function replace($sql, $params = [])
    {
        $this->saveLogs($sql,$params);

        $statement = $this->pdo()->prepare($sql);
        $result = $statement->execute($params);
        $this->rowCount = $statement->rowCount();
        $this->lastInsertId = $this->pdo()->lastInsertId();
        return $result;
    }

    /**
     * 执行更新
     * @param $sql
     * @param array $params
     * @return bool
     */
    public function update($sql, $params = [])
    {
        $this->saveLogs($sql,$params);

        $statement = $this->pdo()->prepare($sql);
        $result = $statement->execute($params);
        $this->rowCount = $statement->rowCount();
        $this->lastInsertId = 0;
        return $result;
    }

    /**
     * 执行删除
     * @param $sql
     * @param array $params
     * @return bool
     */
    public function delete($sql, $params = [])
    {
        $this->saveLogs($sql,$params);

        $statement = $this->pdo()->prepare($sql);
        $result = $statement->execute($params);
        $this->rowCount = $statement->rowCount();
        $this->lastInsertId = 0;
        return $result;
    }

    /**
     * 执行查询
     * @param $sql
     * @param array $params
     * @return array
     */
    public function select($sql, $params = [])
    {
        $this->saveLogs($sql,$params);

        $statement = $this->pdo()->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 返回最后插入行的ID或序列值
     * @param string $name
     * @return mixed
     */
    public function lastInsertId($name = '')
    {
        return $this->lastInsertId;
    }

    /**
     * 返回受上一个 SQL 语句影响的行数
     * @return int
     */
    public function rowCount()
    {
        return $this->rowCount;
    }

    /**
     * 数据库信息
     * @return array
     */
    public function info()
    {
        $output = [
            'server' => 'SERVER_INFO',
            'driver' => 'DRIVER_NAME',
            'client' => 'CLIENT_VERSION',
            'version' => 'SERVER_VERSION',
            'connection' => 'CONNECTION_STATUS'
        ];

        foreach ($output as $key => $value)
        {
            $output[ $key ] = $this->pdo()->getAttribute(constant('PDO::ATTR_' . $value));
        }

        return $output;
    }

    /**
     * 最后执行的sql
     * @return sting
     */
    public function lastSql()
    {
        $log = end($this->logs);
        $sql = $log['sql'];
        foreach ($log['params'] as $key=>$value){
            $sql = str_replace(':'.$key,$this->pdo()->quote($value),$sql);
        }
        return $sql;
    }

    /**
     * sql执行日志
     * @return array
     */
    public function log()
    {
        return $this->logs;
    }

}