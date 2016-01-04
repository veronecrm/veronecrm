<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\User\Group;

use System\Database\Database;

class Manager
{
    private $db;
    private $treeResultTemp = [];

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function find($id)
    {
        return $this->db->builder()->resultAs('CRM\User\Group\Entity')->select('*')->from('#__user_group')->where('id', $id)->one();
    }

    public function getTree()
    {
        $result = $this->_createTree(0);

        $this->treeResultTemp = [];

        return $result;
    }

    private function _createTree($parent, $depth = 0, $returnCount = false)
    {
        $groups = $this->db->builder()->resultAs('CRM\User\Group\Entity')->select('*')->from('#__user_group')->where('parent', $parent)->all();

        foreach($groups as $group)
        {
            $group->depth = $depth;

            $this->treeResultTemp[] = $group;

            $count = $this->_createTree($group->getId(), $depth + 1, true);

            if($count)
            {
                $group->isLast = false;
            }
            else
            {
                $group->isLast = true;
            }
        }

        if($returnCount)
        {
            return count($groups);
        }
        else
        {
            return $this->treeResultTemp;
        }
    }
}
