<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\ORM;

use System\Database\Database;
use System\Routing\Routing;
use System\DependencyInjection\Container;
use CRM\ORM\Repository\Manager as RepositoryManager;
use CRM\ORM\Entity\Manager as EntityManager;

class ORM
{
    /**
     * @var \PDO
     */
    private $connection;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var string
     */
    private $module;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var RepositoryManager
     */
    private $repository;

    /**
     * @var EntityManager
     */
    private $entity;

    /**
     * @param ServiceContainer $container
     * @param Database         $db
     * @param Routing          $routing
     */
    public function __construct(Container $container, Database $db, Routing $routing)
    {
        $this->connection = $db->connection();
        $this->dbPrefix   = $db->prefix();
        $this->module     = $routing->getRoute()->getModule();

        $this->repository = new RepositoryManager($this, $container);
        $this->entity     = new EntityManager($this);
    }

    /**
     * @return \PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Returns RemositoryManager object.
     * @return RepositoryManager
     */
    public function repository()
    {
        return $this->repository;
    }

    /**
     * Returns EntityManager object.
     * @return EntityManager
     */
    public function entity()
    {
        return $this->entity;
    }

    /**
     * Returns prefix to DB tables.
     * @return string
     */
    public function getPrefix()
    {
        return $this->dbPrefix;
    }

    /**
     * Returns name of current Module.
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }
}
