<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM;

class Version
{
    /**
     * Stores version of CRM.
     * @var string
     */
    const VERSION = '0.1.0';

    /**
     * Stores MAJOR version of CRM.
     * @var string
     */
    const MAJOR = '0';

    /**
     * Stores MINOR version of CRM.
     * @var string
     */
    const MINOR = '1';

    /**
     * Stores PATCH version of CRM.
     * @var string
     */
    const PATCH = '0';

    /**
     * Stores release date of CRM.
     * @var string
     */
    const RELEASE_DATE = '2015-10-29';

    /**
     * Check if CRM version is newer than given.
     * @param  string  $version Version to compare.
     * @return boolean
     */
    public function isNewerThan($version)
    {
        return version_compare($version, static::VERSION, '<');
    }

    /**
     * Check if CRM version is older than given.
     * @param  string  $version Version to compare.
     * @return boolean
     */
    public function isOlderThan($version)
    {
        return version_compare($version, static::VERSION, '>');
    }
}
