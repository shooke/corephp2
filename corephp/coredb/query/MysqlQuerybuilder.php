<?php
/**
 * Created by PhpStorm.
 * User: shooke
 * Date: 17-5-4
 * Time: 上午10:08
 */

namespace corephp\coredb\query;


class MysqlQuerybuilder extends Query
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
    private function _parseWhere()
    {

    }
    private function _parseSelect()
    {

    }
    private function _parseFrom()
    {

    }
    private function _parseData()
    {}
    private function _parseJoin()
    {}
    private function _parseGroupBy()
    {}
    private function _parseOrderBy()
    {}
    private function _parseLimit()
    {}
    public function selectSql()
    {}
    public function insertSql()
    {}
    public function updateSql()
    {}

}