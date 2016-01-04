<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

include_once BASEPATH.'/vendor/autoload.php';

use Symfony\Component\ClassLoader\ClassLoader;

$loader = new ClassLoader();
$loader->addPrefix('CRM', BASEPATH.'/core');
$loader->addPrefix('System', BASEPATH.'/core');
$loader->addPrefix('App', BASEPATH.'/app');
$loader->addPrefix('Language', BASEPATH.'/app');
$loader->addPrefix('Helper', BASEPATH.'/app');
$loader->addPrefix('EEHandler', BASEPATH.'/EEHandler');
$loader->register();
