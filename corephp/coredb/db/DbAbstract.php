<?php
/**
 * Created by PhpStorm.
 * User: shooke
 * Date: 17-5-2
 * Time: 下午2:02
 */

namespace corephp\coredb\db;


abstract class DbAbstract
{
    /**
     * 事务开启层级
     * @var int
     */
    protected $transactionCounter = 0;

    /**
     * 执行一段事务
     * $obj->transaction(function($obj){
     *     $obj->query....
     * })
     * @param $callable
     * @return bool
     * @throws Exception
     */
    public function transaction($callable)
    {
        if (!is_callable($callable)) {
            return false;
        }
        try {
            $this->beginTransaction();
            call_user_func($callable, $this);
            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollback();
            throw new Exception($e->getMessage());
        }

    }

    /**
     * 开启事务
     * @return bool
     */
    public function beginTransaction()
    {
        if (!$this->transactionCounter++)
            return $this->pdo()->beginTransaction();
        return $this->transactionCounter >= 0;
    }

    /**
     * 提交事务
     * @return bool
     */
    public function commit()
    {
        if (!--$this->transactionCounter)
            return $this->pdo()->commit();
        return $this->transactionCounter >= 0;
    }

    /**
     * 回滚事务
     * @return bool
     */
    public function rollback()
    {
        if ($this->transactionCounter >= 0) {
            $this->transactionCounter = 0;
            return $this->pdo()->rollback();
        }
        $this->transactionCounter = 0;
        return false;
    }

    /**
     * 根据数据库配置创建或返回连接
     * @return PDO|null
     * @throws PDOException
     */
    abstract public function pdo();

    /**
     * 执行插入
     * @param $sql
     * @param array $params
     * @return bool
     */
    abstract public function insert($sql,$params=[]);
    /**
     * 执行替换式插入
     * @param $sql
     * @param array $params
     * @return bool
     */
    abstract public function replace($sql,$params=[]);
    /**
     * 执行更新
     * @param $sql
     * @param array $params
     * @return bool
     */
    abstract public function update($sql,$params=[]);
    /**
     * 执行删除
     * @param $sql
     * @param array $params
     * @return bool
     */
    abstract public function delete($sql,$params=[]);
    /**
     * 执行查询
     * @param $sql
     * @param array $params
     * @return array
     */
    abstract public function select($sql,$params=[]);

    /**
     * 返回最后插入行的ID或序列值
     * @param string $name
     * @return mixed
     */
    abstract public function lastInsertId($name='');

    /**
     * 返回受上一个 SQL 语句影响的行数
     * @return int
     */
    abstract public function rowCount();

    /**
     * 数据库信息
     * @return array
     */
    abstract public function info();

    /**
     * 最后执行的sql
     * @return sting
     */
    abstract public function lastSql();

    /**
     * sql执行日志
     * @return array
     */
    abstract public function log();
}