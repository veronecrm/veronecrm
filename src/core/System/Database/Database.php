<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Database;

class Database
{
    private $connection;

    private $queries = [];

    private $prefix = '';

    public function __construct(Connector $connector)
    {
        $this->connection = $connector->getConnection();
        $this->prefix     = $connector->getConfig()->get('prefix');
    }

    public function connection()
    {
        return $this->connection;
    }

    public function prefix()
    {
        return $this->prefix;
    }

    public function getLastInsertedId()
    {
        return $this->connection->lastInsertId();
    }

    public function builder()
    {
        return new QueryBuilder($this);
    }
    
    public function query($query)
    {
        return $this->saveQuery($query)->connection->query($this->prepareTableName($query))->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function rawQuery($query)
    {
        return $this->saveQuery($query)->connection->query($this->prepareTableName($query));
    }
    
    public function exec($query)
    {
        return $this->saveQuery($query)->connection->exec($this->prepareTableName($query));
    }
    
    public function saveQuery($query)
    {
        if($query)
        {
            $this->queries[] = $query;
        }

        return $this;
    }
    
    public function getAllQueries()
    {
        return $this->queries;
    }
    
    public function prepareTableName($query)
    {
        if(preg_match('/(\s|\`)\#\_\_([a-z0-9\-\_]+)(\s|\`)/i', " $query ", $matches))
        {
            $query = str_replace('#__', $this->prefix, $query);
        }
        
        return trim($query);
    }
}
