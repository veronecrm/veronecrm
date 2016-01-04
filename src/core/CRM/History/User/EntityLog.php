<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\History\User;

use System\Database\Database;
use CRM\ORM\ORM;
use CRM\ORM\Entity;
use CRM\User\UserIdentityInterface;
use CRM\History\User\Log as BaseLog;

class EntityLog extends BaseLog
{
    protected $entity;
    protected $repository;

    /**
     * This class extends default class, by operating on Entity.
     * @param Entity $entity Entity, to manage.
     */
    public function setEntity(Entity $entity)
    {
        $this->entity = $entity;

        $this->setEntityName($entity->getEntityName());
        $this->setEntityId($entity->getId());

        $this->repository = $this->orm->repository($this->entityName, $this->module)->get($this->entityName, $this->module);

        return $this;
    }

    /**
     * Collects values from Entity before update/save.
     * @return self
     */
    public function storePreValues()
    {
        foreach($this->entity->exportToArray() as $field => $value)
        {
            $this->appendPreValue($field, $this->repository->getEndValue($this->entity, $field));
        }

        return $this;
    }

    /**
     * Collects values from Entity after update/save.
     * @return self
     */
    public function storePostValues()
    {
        foreach($this->entity->exportToArray() as $field => $value)
        {
            $this->appendPostValue($field, $this->repository->getEndValue($this->entity, $field));
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function flush($status, $object)
    {
        return $this->storePostValues()->flushOnly($status, $object);
    }

    protected function findChanges($log)
    {
        $values = $this->db->builder()->select('*')
            ->from('#__history_change')
            ->where('change', $log)
            ->all();

        if(is_array($values))
        {
            $fields = $this->repository->getFieldsNames();

            foreach($values as $val)
            {
                foreach($fields as $field => $name)
                {
                    if($field == $val->field)
                    {
                        // $val is object so we must not change value by index in $values array.
                        $val->field = $name;
                    }
                }
            }
        }

        return $values;
    }
}
