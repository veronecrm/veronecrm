<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

return [
    [
        'name' => '',
        'id' => 'mod.{MODULE}',
        'access' => [
            // All methods in module. Is added automaticaly
            // by ACL to each action in each controller.
            [
                'id' => 'core.module',
                'name' => 'auth.core.module'
            ],
            // Read actions
            [
                'id' => 'core.read',
                'name' => 'auth.core.read'
            ],
            // Write actions
            [
                'id' => 'core.write',
                'name' => 'auth.core.write'
            ],
            // Delete actions
            [
                'id' => 'core.delete',
                'name' => 'auth.core.delete'
            ],
            // Edit own data (added by user, and can be edited only by this user)
            // This setting is not added by default.
            /*[
                'id' => 'core.edit.own',
                'name' => ''
            ]*/
        ]
    ]
];
