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

class Acl
{
    private $db;
    private $user;

    public function __construct(Database $db, UserIdentityInterface $user)
    {
        $this->db   = $db;
        $this->user = $user;
    }

    public function open($section, $entity, $group = null)
    {
        return new Access($this, $this->db, $section, $entity, $group === null ? $this->user->getGroup() : $group);
    }

    public function findGroup($group = null)
    {
        return $this->db->builder()
            ->select('*')
            ->from('#__user_group')
            ->where('id', $group === null ? $this->user->getGroup() : $group)
            ->one();
    }
}
