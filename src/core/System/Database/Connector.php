<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Database;

use System\Config\Config;

class Connector
{
    private $connection;
    private $config;

    public function connect(Config $config)
    {
        $this->config     = $config;

        $this->connection = new \PDO("mysql:host={$config->get('host')};dbname={$config->get('name')}".($config->get('port') != '' ? ';port='.$config->get('port') : ''), $config->get('user'), $config->get('pass'), [ \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'" ]);
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
