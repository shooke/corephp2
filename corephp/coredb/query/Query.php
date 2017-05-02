<?php
/**
 * Created by PhpStorm.
 * User: shooke
 * Date: 17-5-2
 * Time: 下午4:34
 */

namespace corephp\coredb\query;


class Query
{
    protected $select;
    protected $from;
    protected $where;
    protected $data;
    protected $join;
    protected $groupBy;
    protected $orderBy;
    protected $limit = [];
    protected $params = [];


    public function table($table)
    {
        $this->from = $table;
        return $this;
    }

    public function select($field)
    {
        $this->select = $field;
        return $this;
    }

    public function data($data)
    {
        $this->data = $data;
    }

    public function groupBy($groupBy)
    {
        $this->groupBy = $groupBy;
        return $this;
    }

    public function orderBy($orderBy)
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }


    /////////////////////////////////////////////////////////////////////
    //                            sql聚合函数处理                        //
    /////////////////////////////////////////////////////////////////////

    public function count($field = '*')
    {
        $this->select = "count({$field})";
        return $this;
    }

    public function max($field)
    {
        $this->select = "max({$field})";
        return $this;
    }

    public function min($field)
    {
        $this->select = "min({$field})";
        return $this;
    }

    public function avg($field)
    {
        $this->select = "avg({$field})";
        return $this;
    }

    public function sum($field)
    {
        $this->select = "sum({$field})";
        return $this;
    }

    /////////////////////////////////////////////////////////////////////
    //                            where处理                             //
    /////////////////////////////////////////////////////////////////////

    /**
     * 原生查询
     * @param $condition
     * @param $params
     * @return $this
     */
    public function whereRaw($condition, $params)
    {
        $this->where = $condition;
        $this->addParams($params);
        return $this;
    }

    /**
     * 查询
     * @param $condition
     * @return $this
     */
    public function where($condition)
    {
        $this->where = $condition;
        return $this;
    }

    /**
     * and查询
     * @param $condition
     * @return $this
     */
    public function andWhere($condition)
    {
        $key = 'and #' . rand(1, 999999999);
        $this->where = [
            $key => [
                $this->where,
                $condition
            ]
        ];
        return $this;
    }

    /**
     * or查询
     * @param $condition
     * @return $this
     */
    public function orWhere($condition)
    {
        $key = 'or #' . rand(1, 999999999);
        $this->where = [
            $key => [
                $this->where,
                $condition
            ]
        ];
        return $this;
    }

    /**
     * 过滤空条件
     * @param $condition
     * @return $this
     */
    public function filterWhere($condition)
    {
        $this->where = $this->filter($condition);
        return $this;
    }

    /**
     * 过滤空条件
     * @param $condition
     * @return $this
     */
    public function andFilterWhere($condition)
    {
        $this->andWhere($this->filter($condition));
        return $this;
    }

    /**
     * 过滤空条件
     * @param $condition
     * @return $this
     */
    public function orFilterWhere($condition)
    {
        $this->orWhere($this->filter($condition));
        return $this;
    }


    /////////////////////////////////////////////////////////////////////
    //                            join处理                              //
    /////////////////////////////////////////////////////////////////////

    /**
     * inner join处理
     * $condition 可以是数组，也可以是字符串
     * @param $table
     * @param $condition
     * @return $this
     */
    public function innerJoin($table, $condition)
    {
        $this->join[] = [
            'from'  => $table,
            'inner' => $condition
        ];
        return $this;
    }

    public function fullJoin($table, $condition)
    {
        $this->join[] = [
            'from' => $table,
            'full' => $condition
        ];
        return $this;
    }

    public function leftJoin($table, $condition)
    {
        $this->join[] = [
            'from' => $table,
            'left' => $condition
        ];
        return $this;
    }

    public function rightJoin($table, $condition)
    {
        $this->join[] = [
            'from'  => $table,
            'right' => $condition
        ];
        return $this;
    }


    /**
     * 进行过滤处理
     * @param $condition
     * @return mixed
     */
    protected function filter($condition)
    {
        if (is_array($condition)) {
            foreach ($condition as $name => $value) {
                if ($this->isEmpty($value)) {
                    unset($condition[$name]);
                }
            }
        }
        return $condition;
    }

    /**
     * 处理参数
     * @param $params
     */
    protected function addParams($params)
    {
        foreach ($params as $key => $value) {
            $this->params[$key] = $value;
        }
    }

    /**
     * 空字符串 空数组 null 全空格 都当做空处理
     * @param $value
     * @return bool
     */
    protected function isEmpty($value)
    {
        return $value === '' || $value === [] || $value === null || is_string($value) && trim($value) === '';
    }
}