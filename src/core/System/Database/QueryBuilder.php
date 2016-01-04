<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Database;

class QueryBuilder
{
    private $db;

    private $select;
    private $from;
    private $where;
    private $start;
    private $limit;
    private $order;
    private $group;

    private $resultAs = 'stdClass';

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function resultAs($as)
    {
        $this->resultAs = $as;

        return $this;
    }

    public function select($select = '*')
    {
        if($this->select)
        {
            $this->select .= ', '.$select;
        }
        else
        {
            $this->select = $select;
        }

        return $this;
    }

    public function count($column, $as = 'count')
    {
        return $this->select("COUNT('{$column}') AS {$as}");
    }

    public function max($column, $as = 'max')
    {
        return $this->select("MAX('{$column}') AS {$as}");
    }

    public function min($column, $as = 'min')
    {
        return $this->select("MIN('{$column}') AS {$as}");
    }

    public function from($from)
    {
        $this->from = $from;

        return $this;
    }

    public function where($column, $value, $condition = '=', $glue = 'AND')
    {
        $this->where[] = [
            'column'    => $column,
            'value'     => $value,
            'type'      => is_integer($value) ? 'int' : 'str',
            'condition' => $condition,
            'glue'      => $glue
        ];

        return $this;
    }

    public function whereNot($column, $value)
    {
        $this->where($column, $value, '!=');

        return $this;
    }

    public function orWhere($column, $value, $condition = '=')
    {
        $this->where($column, $value, $condition, 'OR');

        return $this;
    }

    public function orWhereNot($column, $value, $condition = '=')
    {
        $this->where($column, $value, '!=', 'OR');

        return $this;
    }

    public function orderBy($column, $type)
    {
        if($this->order == null)
            $this->order = " ORDER BY {$column} ".strtoupper($type);
        else
            $this->order = "{$this->order}, {$column} ".strtoupper($type);
        
        return $this;
    }

    public function groupBy($column)
    {
        $this->group = "GROUP BY {$column}";

        return $this;
    }

    public function limit($limit, $start = null)
    {
        $this->limit = $limit;
        $this->start = $start;

        return $this;
    }

    public function all()
    {
        $query = $this->db->prepareTableName($this->createSelectQuery());

        $this->db->saveQuery($query);

        return $this->createResult($this->db->rawQuery($query), 2);
    }

    public function one()
    {
        $this->limit = 1;
        $this->start = 0;

        $query = $this->db->prepareTableName($this->createSelectQuery());

        $this->db->saveQuery($query);

        return $this->createResult($this->db->rawQuery($query), 1);
    }

    public function insert($table, array $data)
    {
        $query   = "INSERT INTO `{$table}` ";
        $columns = [];
        $values  = [];

        foreach($data as $key => $val)
        {
            $columns[] = "`{$key}`";
            $values[]  = "'".addslashes($val)."'";
        }
        
        $query = $this->db->prepareTableName($query.'('.(implode(', ', $columns).') VALUES ('.implode(', ', $values).')'));

        $this->db->saveQuery($query);

        return $this->db->exec($query);
    }

    public function update($table, array $data)
    {
        $query = "UPDATE `{$table}` SET ";
        $segments = [];

        foreach($data as $key => $val)
        {
            $segments[] = "`{$key}` = '".addslashes($val)."'";
        }

        $query = $query.implode(', ', $segments);
        $where = $this->prepareWhereStatement();

        if($where)
        {
             $query =  "$query WHERE $where";
        }
        
        $query = $this->db->prepareTableName($query);

        $this->db->saveQuery($query);

        return $this->db->exec($query);
    }

    public function delete($table)
    {
        return $this->db->exec($this->db->prepareTableName("DELETE FROM `{$table}` WHERE {$this->prepareWhereStatement()}"));
    }

    public function createSelectQuery()
    {
        if($this->select == '')
        {
            $this->select = '*';
        }

        $query = "SELECT $this->select FROM $this->from";
        $where = $this->prepareWhereStatement();

        if($where)
        {
             $query =  "$query WHERE $where";
        }

        if($this->order)
        {
            $query =  "$query $this->order";
        }

        if($this->group)
        {
            $query =  "$query $this->group";
        }

        if($this->limit)
        {
            $query =  "$query LIMIT ".($this->start ? "$this->start, $this->limit" : $this->limit);
        }

        return $query;
    }

    private function prepareWhereStatement()
    {
        $where = '';

        if($this->where)
        {
            foreach($this->where as $sub)
            {
                if($sub['type'] == 'str')
                {
                    $sub['value'] = "'".addslashes($sub['value'])."'";
                }

                $where .= ($where == '' ? '' : $sub['glue'])." `{$sub['column']}` {$sub['condition']} {$sub['value']} ";
            }
        }

        return $where;
    }

    public function createResult($stmt, $count)
    {
        $result = null;

        if($count === 1)
        {
            if($this->resultAs == 'array')
            {
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            }
            else
            {
                $result = $stmt->fetchObject($this->resultAs);
            }
        }
        else
        {
            if($this->resultAs == 'array')
            {
                $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
            else
            {
                $result = array();

                while($row = $stmt->fetchObject($this->resultAs))
                {
                    $result[] = $row;
                }
            }
        }

        $stmt->closeCursor();

        return $result;
    }
}
