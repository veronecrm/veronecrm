<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Permission;

use System\Database\Database;
use CRM\User\UserIdentityInterface;

class Access
{
    protected $acl;
    protected $db;
    protected $section;
    protected $entity;
    protected $group;

    public function __construct(Acl $acl, Database $db, $section, $entity, $group)
    {
        $this->acl      = $acl;
        $this->db       = $db;
        $this->section  = $section;
        $this->entity   = $entity;
        $this->group    = $group;
    }

    public function getAccess($access)
    {
        $result = $this->get($access);

        if(! $result)
        {
            return 0;
        }
        else
        {
            return $result->permission;
        }
    }

    public function isAllowed($access, $group = null)
    {
        $details = $this->get($access);

        if(! $details)
        {
            return true;
        }

        if($details->permission == 0)
        {
            return $this->isParentAllowed($access, $group === null ? $this->group : $group);
        }

        if($details->permission == 1)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function isParentAllowed($access, $group = null)
    {
        $details = $this->acl->findGroup($group === null ? $this->group : $group);

        if(! $details)
        {
            return 2;
        }

        $parent = $this->acl->findGroup($details->parent);

        if(! $parent)
        {
            return 2;
        }

        return $this->isAllowed($access, $parent->id);
    }

    public function allow($access)
    {
        $this->updateAccess($access, 2);
    }

    public function deny($access)
    {
        $this->updateAccess($access, 1);
    }

    public function inherit($access)
    {
        $this->updateAccess($access, 0);
    }

    protected function updateAccess($access, $permission)
    {
        $result = $this->get($access);

        if(! $result)
        {
            $this->insert($access, $permission);
        }
        else
        {
            $this->update($access, $permission);
        }
    }

    protected function get($access)
    {
        return $this->db->builder()
            ->select('permission')
            ->from('#__permission')
            ->where('access',  $access)
            ->where('entity',  $this->entity)
            ->where('section', $this->section)
            ->where('group',   $this->group)
            ->one();
    }

    protected function insert($access, $permission)
    {
        $this->db->builder()->insert('#__permission', [
            'access'      => $access,
            'entity'      => $this->entity,
            'section'     => $this->section,
            'group'       => $this->group,
            'permission'  => $permission
        ]);
    }

    protected function update($access, $permission)
    {
        $this->db->builder()
            ->where('access',  $access)
            ->where('entity',  $this->entity)
            ->where('section', $this->section)
            ->where('group',   $this->group)
            ->update('#__permission', [ 'permission' => $permission ]);
    }
}
