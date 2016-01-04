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
use CRM\User\UserIdentityInterface;

class Log
{
    protected $db;
    protected $orm;
    protected $user;
    protected $module;
    protected $entityName;
    protected $entityId;
    protected $log = null;
    protected $type = 1;

    /**
     * Store ID of row in DB, that contains
     * pre and post values. Can be used for
     * creating relations of many changes.
     * @var integer
     */
    protected $changeId;

    /**
     * Store ID of other change, that is related with this one.
     * This allow us create relational changes. We change A entity,
     * and also B and C entity, so we can create relation, to save
     * this change with other one. Given only for changes, that
     * was created by other changes - not for main changes.
     * @var integer
     */
    protected $relatedWith;

    /**
     * Stores values before changing by user
     * (these values was selected from DB).
     * @var array
     */
    public $preValues = [];

    /**
     * Stores values after changing by user
     * (these values will be saved in DB).
     * @var array
     */
    public $postValues = [];

    /**
     * @param Database              $db     Database object.
     * @param ORM                   $orm    ORM object.
     * @param UserIdentityInterface $user   Current logged user object.
     */
    public function __construct(Database $db, ORM $orm, UserIdentityInterface $user)
    {
        $this->db   = $db;
        $this->orm  = $orm;
        $this->user = $user;
    }

    /**
     * Return Change ID, that give us ability to create
     * relation of multiple changes.
     * @return integer
     */
    public function getChangeId()
    {
        return $this->changeId;
    }

    /**
     * Set ID of ohte change (row in DB), that this change should be related.
     * @param  integer $relatedWith ID of other change/
     * @return selg
     */
    public function relatedWith($relatedWith)
    {
        $this->relatedWith = $relatedWith;

        return $this;
    }

    /**
     * Set Entity identificator that idetifies it with Entity in other Table in DB.
     * @param integer $entityId
     * @return self
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Set Entity name.
     * @param string $entityName
     * @return self
     */
    public function setEntityName($entityName)
    {
        $this->entityName = $entityName;

        return $this;
    }

    /**
     * Set Module name.
     * @param string $module
     * @return self
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Save log as standard text.
     * @param  string $log              Log's text to save.
     * @param  string|integer $status   Log status.
     * @param  string $module           Module name.
     * @return self
     */
    public function log($log, $status, $module)
    {
        $this->log    = $log;
        $this->type   = 2;
        $this->setModule($module);

        $this->flushOnly($status, null);

        $this->log    = null;
        $this->type   = 1;

        return $this;
    }

    /**
     * Append field name and value, before update.
     * @param  string $field Field name of value.
     * @param  mixed  $value Value before update.
     * @return self
     */
    public function appendPreValue($field, $value)
    {
        $this->preValues[$field] = $value;

        return $this;
    }

    /**
     * Append field name and value, after update.
     * @param  string $field Field name of value.
     * @param  mixed  $value Value after update.
     * @return self
     */
    public function appendPostValue($field, $value)
    {
        $this->postValues[$field] = $value;

        return $this;
    }

    /**
     * Saves difference between pre-and post-data from Entity in database.
     * @param  integer $status Numerical status of change.
     *                         Allowed: 1 - create; 2 - update; 3 - delete
     * @param  string  $object Name of object which was updated, can be translated text.
     * @return self
     */
    public function flush($status, $object)
    {
        return $this->flushOnly($status, $object);
    }

    /**
     * Saves difference between pre-and post-data from Entity in database.
     * @param  integer $status Numerical status of change.
     *                         Allowed: 1 - create; 2 - update; 3 - delete
     * @param  string  $object Name of object which was updated, can be translated text.
     * @return self
     */
    public function flushOnly($status, $object)
    {
        $status = str_replace(['create', 'change', 'delete'], [1, 2, 3], $status);

        $this->db->builder()->insert('#__history_log', [
            'authorId'   => $this->user->getId(),
            'authorName' => $this->user->getName(),
            'entityId'   => $this->entityId,
            'date'       => time(),
            'object'     => $object,
            'status'     => $status,
            'module'     => $this->module,
            'entityName' => $this->entityName,
            'relatedWith'=> $this->relatedWith,
            'type'       => $this->type,
            'log'        => $this->log
        ]);

        $this->changeId = $this->db->getLastInsertedId();

        if($status == 1)
            $this->savePreData($this->changeId);
        else
            $this->saveChanges($this->changeId);

        return $this;
    }

    /**
     * Generates array of changes generated in given Entity.
     * @return array Array of details.
     */
    public function generateSummaryEdit($start = null, $limit = null)
    {
        $changes = $this->findLogs($start, $limit);

        foreach($changes as $change)
        {
            $values = $this->findChanges($change->id);

            if(is_array($values))
            {
                $change->changes = $values;
            }
            else
            {
                $change->changes = [];
            }
        }

        if($start !== null && $limit !== null)
        {
            return [
                'total'   => $this->countLogs(),
                'changes' => $changes
            ];
        }
        else
        {
            return $changes;
        }
    }

    protected function savePreData($changeId)
    {
        $builder = $this->db->builder();

        foreach($this->preValues as $fieldPre => $valPre)
        {
            if((is_string($valPre) && $valPre != '') || (! is_string($valPre) && $valPre != null))
            {
                $builder->insert('#__history_change', [
                    'change' => $changeId,
                    'field'  => $fieldPre,
                    'pre'    => $valPre
                ]);
            }
        }
    }

    protected function saveChanges($changeId)
    {
        $changed = [];

        foreach($this->preValues as $fieldPre => $valPre)
        {
            foreach($this->postValues as $fieldPost => $valPost)
            {
                if($fieldPre === $fieldPost && $valPost !== null && $valPre != $valPost)
                {
                    $changed[] = [
                        'name' => $fieldPost,
                        'pre'  => $valPre,
                        'post' => $valPost
                    ];
                }
            }
        }

        $builder = $this->db->builder();

        foreach($changed as $val)
        {
            $builder->insert('#__history_change', [
                'change' => $changeId,
                'field'  => $val['name'],
                'pre'    => $val['pre'],
                'post'   => $val['post']
            ]);
        }
    }

    protected function findLogs($start = null, $limit = null)
    {
        $query = $this->db->builder()->select('*')
            ->from('#__history_log')
            ->where('module', $this->module)
            ->where('entityName', $this->entityName)
            ->where('entityId', $this->entity->getId())
            ->where('relatedWith', 0)
            ->orderBy('date', 'desc');

        if($start !== null && $limit !== null)
        {
            $query->limit($limit, $start);
        }

        return $query->all();
    }

    protected function findChanges($log)
    {
        return $this->db->builder()->select('*')
            ->from('#__history_change')
            ->where('change', $log)
            ->all();
    }

    protected function countLogs()
    {
        $result = $this->db->builder()
            ->count('id')
            ->from('#__history_log')
            ->where('module', $this->module)
            ->where('entityName', $this->entityName)
            ->where('entityId', $this->entityId)
            ->all();

        if(isset($result[0]) && property_exists($result[0], 'count'))
        {
            return $result[0]->count;
        }
        else
        {
            return 0;
        }
    }
}
