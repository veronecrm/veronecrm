<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

return [
    'bootstrap' => [
        'class' => 'CRM\Bootstrap',
        'arguments' => [
            'container'
        ],
        'listen' => [
            'onAppStart',
            'onAppClose'
        ]
    ],
    'translation' => [
        'class' => 'System\Translation\Container',
    ],
    'translationBuilder' => [
        'class' => 'CRM\Translation\Builder',
        'arguments' => [
            'container'
        ],
        'listen' => [
            'onAppStart'
        ]
    ],
    'orm' => [
        'class' => 'CRM\ORM\ORM',
        'arguments' => [
            'container',
            'database',
            'routing'
        ]
    ],
    'settings' => [
        'class' => 'System\Settings\Settings'
    ],
    'history.user.log' => [
        'class' => 'CRM\History\User\Log',
        'always-new' => true,
        'arguments' => [
            'database',
            'orm',
            'user'
        ]
    ],
    'history.user.entitylog' => [
        'class' => 'CRM\History\User\EntityLog',
        'always-new' => true,
        'arguments' => [
            'database',
            'orm',
            'user'
        ]
    ],
    'package.module.manager' => [
        'class' => 'CRM\Package\Module\ModuleManager',
        'arguments' => [
            'container',
            'database'
        ]
    ],
    'package.plugin.manager' => [
        'class' => 'CRM\Package\Module\PluginManager',
        'arguments' => [
            'container',
            'package.module.manager',
            'settings'
        ]
    ],
    'package.library.manager' => [
        'class' => 'CRM\Package\Library\LibraryManager',
        'arguments' => [
            'container',
            'database'
        ]
    ],
    'package.update.factory' => [
        'class' => 'CRM\Package\Update\Factory',
        'arguments' => [
            'container'
        ]
    ],
    'package.install.factory' => [
        'class' => 'CRM\Package\Install\Factory',
        'arguments' => [
            'container'
        ]
    ],
    'package.uninstall.factory' => [
        'class' => 'CRM\Package\Uninstall\Factory',
        'arguments' => [
            'container'
        ]
    ],
    'permission.acl' => [
        'class' => 'CRM\Permission\Acl',
        'arguments' => [
            'database',
            'user'
        ]
    ],
    'permission.acl.generator' => [
        'class' => 'CRM\Permission\Generator\PermissionsHtml',
        'arguments' => [
            'container'
        ]
    ],
    'user.group.manager' => [
        'class' => 'CRM\User\Group\Manager',
        'arguments' => [
            'database'
        ]
    ],
    'localisationResolver' => [
        'class' => 'System\Locale\LocalisationResolver'
    ],
    'datetime' => [
        'class' => 'System\Utils\Datetime',
        'use-factory' => true
    ],
    'assetter' => [
        'class' => 'System\Assetter\Assetter'
    ],
    'registration' => [
        'class' => 'CRM\Registration',
        'arguments' => [
            'container'
        ]
    ],
    'helper.currency' => [
        'class' => 'Helper\Currency',
        'use-factory' => true
    ],
    'helper.language' => [
        'class' => 'Helper\Language',
        'use-factory' => true
    ],
    'helper.timezone' => [
        'class' => 'Helper\Timezone',
        'use-factory' => true
    ],
    'helper.measureUnit' => [
        'class' => 'Helper\MeasureUnit',
        'use-factory' => true
    ],
    'helper.tax' => [
        'class' => 'Helper\Tax',
        'use-factory' => true
    ]
];
