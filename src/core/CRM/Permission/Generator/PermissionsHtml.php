<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Permission\Generator;

use System\DependencyInjection\Container;
use CRM\Permission\Module\Config;

class PermissionsHtml
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function generateFor($module)
    {
        $groups = $this->container->get('user.group.manager')->getTree();

        $result = '<div class="tabbable tabs-left">';
        // Generate tabs with Users Groups.
        $result .= $this->groupsTabs($groups);
        // Generates tabs contents with access tables
        $result .= $this->groupsContents($module, $groups);

        return $result.'</div>';
    }

    private function groupsTabs(array $groups)
    {
        $result = '<ul class="nav nav-tabs">';

        foreach($groups as $key => $item)
        {
            $result .= '<li'.($key == 0 ? ' class="active"' : '').'><a href="#'.$item->getId().'" data-toggle="tab">'.str_repeat('&ndash;&nbsp;&nbsp;&nbsp;', $item->depth).$item->getName().'</a></li>';
        }

        return $result.'</ul>';
    }

    private function groupsContents($module, array $groups)
    {
        $acl = $this->container->get('permission.acl');
        $t   = $this->container->get('translation');

        $config = (new Config)->getConfig($module);

        if($config === null)
        {
            return null;
        }

        $result = '<div class="tab-content">';

        foreach($groups as $key => $group)
        {
            $result .= '<div class="tab-pane'.($key == 0 ? ' active' : '').'" id="'.$group->getId().'">';
            $contents = [];
            $contIter = 0;

            foreach($config as $section)
            {
                $contents[$contIter] = '<div class="panel panel-default">
                <div class="panel-heading panel-heading-hl">'.$t->get($section['name']).'</div>
                <div class="panel-body"><table class="table">
                    <thead>
                        <tr>
                            <th>'.$t->get('action').'</th>
                            <th>'.$t->get('setPermission').'</th>
                            <th>'.$t->get('calculatedPermission').'</th>
                        </tr>
                    </thead>
                    <tbody>';

                foreach($section['access'] as $access)
                {
                    $opened     = $acl->open($section['id'], 'mod.'.$module, $group->getId());
                    $allow      = $opened->getAccess($access['id'], 'mod.'.$module, $group->getId());
                    $calculated = $opened->isAllowed($access['id'])
                                                ? '<span class="label label-success">'.$t->get('allowed').'</span>'
                                                : '<span class="label label-danger">'.$t->get('notAllowed').'</span>';

                    $contents[$contIter] .= '<tr>
                                                <td>'.$t->get($access['name']).'</td>
                                                <td>
                                                    <select name="acl['.str_replace('.', '_', $section['id']).']['.str_replace('.', '_', $access['id']).']['.$group->getId().']">
                                                        <option value="0"'.($allow == 0 ? ' selected="selected"' : '').'>'.$t->get('inherited').'</option>
                                                        <option value="1"'.($allow == 1 ? ' selected="selected"' : '').'>'.$t->get('notAllowed').'</option>
                                                        <option value="2"'.($allow == 2 ? ' selected="selected"' : '').'>'.$t->get('allowed').'</option>
                                                    </select>
                                                </td>
                                                <td>'.$calculated.'</td>
                                            </tr>';
                }

                $contents[$contIter] .= '</tbody></table></div></div>';
                $contIter++;
            }

            $result .= implode('', $contents).'</div>';
        }

        return $result.'</div>';
    }
}
