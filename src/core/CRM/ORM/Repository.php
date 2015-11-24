<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\ORM;

use CRM\Base;

class Repository extends Base
{
    public $dbTable = '__EMPTY__';

    public $dbTableClean = '__EMPTY__';

    public $dbPrimary = 'id';

    public $dbPrimaryPrepared = ':id';

    public $dbPrefix;

    private $sourceEntity;

    private $name = '';

    private $component = '';

    private $entities = [];

    private $orm;

    /**
     * @param string $name Name of Repository or Entity to manage.
     * @param ORM    $orm
     */
    public function __construct($name, $component, ORM $orm)
    {
        $this->name       = $name;
        $this->component  = $component;
        $this->orm        = $orm;

        $this->dbPrefix   = $this->orm->getPrefix();

        $this->dbTable      = str_replace('#__', $this->dbPrefix, $this->dbTable);
        $this->dbTableClean = str_replace('#__', '', $this->dbTable);
        $this->dbPrimaryPrepared = ':'.$this->dbPrimary;
    }

    /**
     * Returns Entity fields names (should be translated) as array: field => name
     * @return array Array of Enttity fields names.
     */
    public function getFieldsNames()
    {
        return [];
    }

    /**
     * This method should return value of given field from given Entity. If value
     * is external key for some other Entity or some static setting (type of customer eg.),
     * should return identified name (or translated text) of this element.
     *
     * This method in repository should always returns at end, result from parent class method.
     * Method in Base Repository class have default translations for some fields, and
     * Repository should not have to implements it:
     *   return parent::getEndValue($entity, $field);
     * 
     * @param  Entity $entity Entity from value get from.
     * @param  string $field  Field name of entity.
     * @return mixed          Value.
     */
    public function getEndValue(Entity $entity, $field)
    {
        if($field == 'created' && method_exists($entity, 'getCreated') && $entity->getCreated() !== null)
        {
            return $this->datetime()->date($entity->getCreated());
        }
        if($field == 'modified' && method_exists($entity, 'getModified') && $entity->getModified() !== null)
        {
            return $this->datetime($entity->getModified());
        }
        if(method_exists($entity, $entity->createAccessMethod('get', $field)))
        {
            return $entity->{$entity->createAccessMethod('get', $field)}();
        }

        return $field;
    }

    /**
     * Returns Entity object.
     * @param  string $name   Entity name.
     * @param  string $module Module name where find Entity.
     * @return Entity
     */
    public function entity($name = null, $module = null)
    {
        return $this->orm->entity()->get($name, $module);
    }

    /**
     * Sets default Entity object.
     * @param Entity $entity
     * @return self
     */
    public function setSourceEntity(Entity $entity)
    {
        $this->sourceEntity = $entity;

        return $this;
    }

    /**
     * Adds Entity to collection
     * @param  Entity $entity
     * @return self
     */
    public function append(Entity $entity)
    {
        $this->entities[] = $entity;

        return $this;
    }

    /**
     * Saves all Entities in collection.
     * @return self
     */
    public function saveCollection()
    {
        foreach($this->entities as $entity)
        {
            $this->save($entity);
        }

        return $this;
    }

    /**
     * Updates all Entities in collection.
     * @return void
     */
    public function updateCollection()
    {
        foreach($this->entities as $entity)
        {
            $this->update($entity);
        }

        return $this;
    }

    /**
     * Finds one row, by given primaryKey.
     * @param  mixed $pk
     * @return object System\ORM\Entity
     * @return boolean false When no rows founded.
     */
    public function find($pk)
    {
        $result = $this->doPostSelect($this->prepareAndExecute("SELECT * FROM `{$this->dbTable}` WHERE `{$this->dbPrimary}` = {$this->dbPrimaryPrepared} LIMIT 1", [ $this->dbPrimaryPrepared => $pk ], true));

        if(isset($result[0]))
        {
            return $result[0];
        }
        else
        {
            return false;
        }
    }

    /**
     * Finds all elements by given conditions string. Last parameters creates limit of result.
     * @param  string $conditions String with conditions (WHERE)
     * @param  array  $binds      Array with binded Values
     * @param  int    $start
     * @param  int    $limit
     * @return array
     */
    public function findAll($conditions = '', array $binds = [], $start = null, $limit = null)
    {
        $pagination = '';

        if($start !== null && $limit !== null)
        {
            $pagination = "LIMIT $start, $limit";
        }

        if($conditions != '')
        {
            $conditions = "WHERE {$conditions}";
        }

        return $this->doPostSelect($this->prepareAndExecute("SELECT * FROM `{$this->dbTable}` {$conditions} {$pagination}", $binds, true));
    }

    /**
     * Alias to findAll without any parameters.
     * @see findAll
     */
    public function all()
    {
        return $this->findAll('', []);
    }

    /**
     * Returns number of rows, by given (if any) conditions.
     * @param  string $conditions String with conditions (WHERE)
     * @param  array  $binds      Array with binded Values
     * @return int
     */
    public function countAll($conditions = '', array $binds = [])
    {
        if($conditions != '')
        {
            $conditions = "WHERE {$conditions}";
        }

        $stmt = $this->orm->getConnection()->prepare("SELECT COUNT(`{$this->dbPrimary}`) AS `count` FROM `{$this->dbTable}` {$conditions}");

        foreach($binds as $key => $val)
        {
            $stmt->bindValue($key, $val, \PDO::PARAM_STR);
        }

        $stmt->execute();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $stmt->closeCursor();
        unset($stmt);

        if(! $result)
        {
            return 0;
        }
        else
        {
            return $result[0]['count'];
        }
    }

    /**
     * Execute SELECT query and bind result for Entity.
     * @param  string $query
     * @param  array  $binds
     * @return array
     */
    public function selectQuery($query, array $binds = [])
    {
        $result = $this->prepareAndExecute($query, $binds, true);

        if(is_array($result))
        {
            foreach($result as $item)
            {
                if(is_object($item) && property_exists($item, 'isNew'))
                {
                    $item->isNew = false;
                }
            }
        }

        return $result;
    }

    /**
     * Execute INSERT query and return affected rows.
     * @param  string $query
     * @param  array  $binds
     * @return int
     */
    public function insertQuery($query, array $binds = [])
    {
        return $this->prepareAndExecute($query, $binds);
    }

    /**
     * Execute UPDATE query and return affected rows.
     * @param  string $query
     * @param  array  $binds
     * @return int
     */
    public function updateQuery($query, array $binds = [])
    {
        return $this->prepareAndExecute($query, $binds);
    }

    /**
     * Execute DELETE query and return affected rows.
     * @param  string $query
     * @param  array  $binds
     * @return int
     */
    public function deleteQuery($query, array $binds = [])
    {
        return $this->prepareAndExecute($query, $binds);
    }

    /**
     * Saves or updates Entity and return affected rows.
     * @param  Entity $entity Entity to save or update.
     * @return integer Affected rows.
     */
    public function save(Entity $entity)
    {
        // If it new, we INSERT it.
        if($entity->isNew)
        {
            $this->onSave($entity);

            // We must tell ORM, in next saves, that this Entity is not new anyway.
            $entity->isNew = false;

            $columns = [];

            foreach($entity->getColumns() as $column)
            {
                if($column == $this->dbPrimary || $this->entityPropertyValue($entity, $column) === null)
                {
                    continue;
                }

                $columns[] = $column;
            }

            $stmt = $this->orm->getConnection()->prepare($this->prepareQuery("INSERT INTO `{$this->dbTable}` (`".implode('`, `', $columns)."`) VALUES (:".implode(', :', $columns).")"));

            foreach($columns as $column)
            {
                if($column == $this->dbPrimary)
                {
                    continue;
                }

                $stmt->bindValue($column, $this->entityPropertyValue($entity, $column), \PDO::PARAM_STR);
            }

            $result = $stmt->execute();

            if($result)
            {
                $this->updateEntityProperty($entity, $this->dbPrimary, $this->orm->getConnection()->lastInsertId());
            }

            return $result;
        }
        // Else, we update
        else
        {
            $this->onUpdate($entity);

            $columnsQuery = [];
            $columns      = [];

            foreach($entity->getColumns() as $column)
            {
                if($column == $this->dbPrimary || $this->entityPropertyValue($entity, $column) === null)
                {
                    continue;
                }

                $columns[] = $column;
            }

            foreach($columns as $column)
            {
                $columnsQuery[] = "`$column` = :$column";
            }

            $stmt = $this->orm->getConnection()->prepare("UPDATE `{$this->dbTable}` SET ".implode(', ', $columnsQuery)." WHERE `{$this->dbPrimary}` = {$this->dbPrimaryPrepared} LIMIT 1");
            $stmt->bindValue($this->dbPrimaryPrepared, $this->entityPropertyValue($entity, $this->dbPrimary), \PDO::PARAM_STR);

            foreach($columns as $column)
            {
                if($column == $this->dbPrimary)
                {
                    continue;
                }

                $stmt->bindValue($column, $this->entityPropertyValue($entity, $column), \PDO::PARAM_STR);
            }

            return $stmt->execute();
        }
    }

    /**
     * Delete given Entity, and return affected rows.
     * @param  Entity $entity Entity to delete.
     * @return integer Affected rows.
     */
    public function delete(Entity $entity)
    {
        $this->onDelete($entity);

        $stmt = $this->orm->getConnection()->prepare("DELETE FROM `{$this->dbTable}` WHERE `{$this->dbPrimary}` = {$this->dbPrimaryPrepared} LIMIT 1");
        $stmt->bindValue($this->dbPrimaryPrepared, $this->entityPropertyValue($entity, $this->dbPrimary), \PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Method is runned, when rows are selected. Method sets in every Entity, that it is not
     * new row, and can be only UPDATED (not INSERTED).
     * @param  mixed $result
     * @return array
     */
    public function doPostSelect($result)
    {
        if(! $result)
            return [];

        foreach($result as $item)
        {
            $item->isNew = false;
            $this->onSelect($item);
        }

        return $result;
    }

    /**
     * Event onSelect.
     * @param  Entity $entity
     * @return void
     */
    public function onSelect(Entity $entity)
    {

    }

    /**
     * Event onSave.
     * @param  Entity $entity
     * @return void
     */
    public function onSave(Entity $entity)
    {

    }

    /**
     * Event onDelete.
     * @param  Entity $entity
     * @return void
     */
    public function onDelete(Entity $entity)
    {

    }

    /**
     * Event onUpdate.
     * @param  Entity $entity
     * @return void
     */
    public function onUpdate(Entity $entity)
    {

    }

    /**
     * Returns PDO connection object.
     * @return PDO
     */
    public function getConnection()
    {
        return $this->orm->getConnection();
    }

    /**
     * Prepares query, binds values and result Entity objects as result of query - only if $fetchObject is set.
     * @param  string  $sql         Query.
     * @param  array   $binds       Array of binds.
     * @param  boolean $fetchObject Tells if records can be returned as array of Entities.
     * @return array                Array of Entities or Array of arrays.
     */
    public function prepareAndExecute($sql, array $binds, $fetchObject = false)
    {
        $stmt = $this->orm->getConnection()->prepare($this->prepareQuery($sql));

        foreach($binds as $key => $val)
        {
            $stmt->bindValue($key, $val, \PDO::PARAM_STR);
        }

        if($fetchObject)
        {
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_CLASS, $this->orm->entity()->generateClassName($this->name, $this->component));
        }
        else
        {
            $result = $stmt->execute();
        }

        $stmt->closeCursor();
        unset($stmt);

        return $result;
    }

    /**
     * Replace markers with table prefix.
     * @param  string $query
     * @return string
     */
    public function prepareQuery($query)
    {
        return str_replace('#__', $this->dbPrefix, $query);
    }

    /**
     * Sets value in property of entity.
     * @param  Entity $entity
     * @param  string $property Property name.
     * @param  mixed  $value    Value to set.
     * @return self
     */
    public function updateEntityProperty(Entity $entity, $property, $value)
    {
        $method = $this->createAccessMethod('set', $property);

        if(method_exists($entity, $method))
        {
            $entity->{$method}($value);
        }
        else
        {
            $entity->{$property} = $value;
        }

        return $this;
    }

    /**
     * Return value of the property.
     * @param  Entity $entity
     * @param  string $property Property name to value get.
     * @return mixed value of given property in given entity.
     */
    public function entityPropertyValue(Entity $entity, $property)
    {
        $method = $this->createAccessMethod('get', $property);

        if(method_exists($entity, $method))
        {
            return $entity->{$method}();
        }
        else
        {
            return $entity->{$property};
        }
    }

    /**
     * Create property value method name by given type (getter or setter).
     * @param  string $type set OR get
     * @param  string $name Name of property
     * @return string
     */
    public function createAccessMethod($type, $name)
    {
        return $type == 'get' ? 'get'.ucfirst($name) : 'set'.ucfirst($name);
    }
}
