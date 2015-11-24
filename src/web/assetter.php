<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

return [
    [
        'name'  => 'jquery',
        'order' => -100,
        'group' => 'head',
        'files' => [ 'js' => [ '{ASSETS}/jquery/jquery.min.js' ] ]
    ],
    [
        'name'  => 'jquery-ui',
        'order' => -90,
        'group' => 'body',
        'files' => [ 'js' => [ '{ASSETS}/jquery-ui/jquery-ui.min.js' ], 'css' => [ '{ASSETS}/jquery-ui/jquery-ui.min.css' ] ],
        'require' => [ 'jquery' ]
    ],
    [
        'name'  => 'jquery-ui.theme',
        'order' => -90,
        'group' => 'body',
        'files' => [ 'css' => [ '{ASSETS}/jquery-ui/jquery-ui.theme.min.css', '{ASSETS}/jquery-ui/jquery-ui.structure.min.css' ] ],
        'require' => [ 'jquery-ui' ]
    ],
    [
        'name'  => 'bs-css',
        'order' => -90,
        'group' => 'head',
        'files' => [ 'css' => [ '{ASSETS}/bootstrap/css/bootstrap.min.css' ] ],
        'require' => [ 'jquery' ]
    ],
    [
        'name'  => 'bs-js',
        'order' => -90,
        'group' => 'body',
        'files' => [ 'js' => [ '{ASSETS}/bootstrap/js/bootstrap.min.js' ] ],
        'require' => [ 'jquery' ]
    ],
    [
        'name'  => 'jquery-cookie',
        'order' => -50,
        'group' => 'body',
        'files' => [ 'js' => [ '{ASSETS}/jquery.cookie/jquery.cookie.js' ] ],
        'require' => [ 'jquery' ]
    ],
    [
        'name'  => 'app-main-css',
        'order' => -20,
        'group' => 'head',
        'files' => [ 'css' => [ '{ASSETS}/app/main.css' ] ]
    ],
    [
        'name'  => 'app-main-js',
        'order' => -20,
        'group' => 'body',
        'files' => [ 'js' => [ '{ASSETS}/app/main.js' ] ]
    ],
    [
        'name'  => 'font-awesome',
        'order' => -90,
        'group' => 'body',
        'files' => [ 'css' => [ '{ASSETS}/font-awesome/css/font-awesome.min.css' ] ]
    ],
    [
        'name'  => 'momentjs',
        'order' => -90,
        'group' => 'body',
        'files' => [ 'js' => [ '{ASSETS}/momentjs/moment-with-locales.min.js' ] ]
    ],
    [
        'name'  => 'datetimepicker',
        'order' => -50,
        'group' => 'body',
        'files' => [ 'js' => [ '{ASSETS}/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js' ], 'css' => [ '{ASSETS}/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css' ] ],
        'require' => [ 'jquery', 'momentjs', 'jquery-ui' ]
    ],
    [
        'name'  => 'jstree',
        'order' => -50,
        'group' => 'body',
        'files' => [ 'js' => [ '{ASSETS}/vakata-jstree/jstree.min.js' ], 'css' => [ '{ASSETS}/vakata-jstree/themes/default/style.min.css' ] ],
        'require' => [ 'jquery' ]
    ],
    [
        'name'  => 'bootbox',
        'order' => -50,
        'group' => 'body',
        'files' => [ 'js' => [ '{ASSETS}/bootbox/bootbox.min.js' ] ],
        'require' => [ 'jquery', 'bs-js' ]
    ],
    [
        'name'  => 'typeahead',
        'order' => -50,
        'group' => 'body',
        'files' => [ 'js' => [ '{ASSETS}/bootstrap-typeahead/bootstrap3-typeahead.min.js' ] ],
        'require' => [ 'jquery' ]
    ],
    [
        'name'  => 'radio-switch',
        'order' => -50,
        'group' => 'body',
        'files' => [ 'js' => [ '{ASSETS}/bootstrap-switch/bootstrap-switch.min.js' ], 'css' => [ '{ASSETS}/bootstrap-switch/bootstrap-switch.min.css' ] ],
        'require' => [ 'jquery' ]
    ],
    [
        'name'  => 'checkbox-toggle',
        'order' => -50,
        'group' => 'body',
        'files' => [ 'js' => [ '{ASSETS}/bootstrap-toggle/js/bootstrap-toggle.min.js' ], 'css' => [ '{ASSETS}/bootstrap-toggle/css/bootstrap-toggle.min.css' ] ],
        'require' => [ 'jquery' ]
    ],
    [
        'name'  => 'input-mask',
        'order' => -50,
        'group' => 'body',
        'files' => [ 'js' => [ '{ASSETS}/jquery.maskedinput/jquery.maskedinput.min.js' ] ],
        'require' => [ 'jquery' ]
    ],
    [
        'name'  => 'howler',
        'order' => -50,
        'group' => 'body',
        'files' => [ 'js' => [ '{ASSETS}/howler.js/howler.min.js' ] ],
        'require' => [ 'jquery' ]
    ],
    [
        'name'  => 'range-slider',
        'order' => -50,
        'group' => 'body',
        'files' => [ 'js' => [ '{ASSETS}/bootstrap-slider/bootstrap-slider.min.js' ], 'css' => [ '{ASSETS}/bootstrap-slider/css/bootstrap-slider.min.css' ] ],
        'require' => [ 'jquery', 'bs-js' ]
    ],
    [
        'name'  => 'magnific-popup',
        'order' => -50,
        'group' => 'body',
        'files' => [ 'js' => [ '{ASSETS}/magnific-popup/jquery.magnific-popup.min.js' ], 'css' => [ '{ASSETS}/magnific-popup/magnific-popup.css' ] ],
        'require' => [ 'jquery' ]
    ],
    [
        'name'  => 'jquery-mousewheel',
        'order' => -50,
        'group' => 'body',
        'files' => [ 'js' => [ '{ASSETS}/jquery.mousewheel/jquery.mousewheel.min.js' ] ],
        'require' => [ 'jquery' ]
    ],
    [
        'name'  => 'mcustomscrollbar',
        'order' => -50,
        'group' => 'body',
        'files' => [ 'js' => [ '{ASSETS}/mcustomscrollbar/jquery.mCustomScrollbar.min.js' ], 'css' => [ '{ASSETS}/mcustomscrollbar/jquery.mCustomScrollbar.min.css' ] ],
        'require' => [ 'jquery-mousewheel' ]
    ],
];
