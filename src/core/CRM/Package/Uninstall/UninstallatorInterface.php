<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Package\Uninstall;

use System\DependencyInjection\Container;

interface UninstallatorInterface
{
    /**
     * Uninstall module from given directory path.
     * @return boolean True of module uninstalled succesfully. False otherwise.
     */
    public function uninstall();

    /**
     * Clean up uninstallation files and temporary files/directories created
     * during uninstallation. Called automatically when installation ends
     * with success. Need to be called manually, when catched any exception.
     * @return void
     */
    public function cleanUp();

    /**
     * Return logs created during uninstallation.
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
     * Return directory where package files are stored.
     * @return string
     */
    public function getPackageDir();

    /**
     * Set ServiceContainer for usage in uninstallation class.
     * @param Container $container
     */
    public function setContainer(Container $container);
}
