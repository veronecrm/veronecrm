<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

$start = microtime(true);

define('BASEPATH', realpath(__DIR__) == '/' ? '/../' : realpath(__DIR__.'/../'));
define('ENVIRONMENT', 'dev');

/**
 * Class Autoloader and Namespaces definitions.
 */
require_once BASEPATH.'/core/autoload.php';

/**
 * Create App.
 */
require_once BASEPATH.'/core/app.php';
