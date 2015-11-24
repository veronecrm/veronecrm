<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Package\Install;

use System\DependencyInjection\Container;

interface InstallatorInterface
{
    /**
     * Install module from given directory path.
     * @return boolean True of module installed succesfully. False otherwise.
     */
    public function install();

    /**
     * Clean up installation files and temporary files/directories created
     * during installation. Called automatically when installation ends
     * with success. Need to be called manually, when catched any exception.
     * @return void
     */
    public function cleanUp();

    /**
     * Return logs created during installation.
     * @param  boolean $asArray Logs should be returned as Array?
     * @return mixed   Array or String according to $asArray parameter.
     */
    public function getLogs($asArray = false);

    /**
     * Return parsed Manifest file object.
     * @return CRM\Package\ManifestParser
     */
    public function getManifest();

    /**
     * Return temporary directory where installation files are stored.
     * @return string
     */
    public function getTemporaryDir();

    /**
     * Set ServiceContainer for usage in installation class.
     * @param Container $container
     */
    public function setContainer(Container $container);
}
