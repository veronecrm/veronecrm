<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\User;

interface UserIdentityInterface
{
    /**
     * Returns ID of User.
     * @return integer
     */
    public function getId();

    /**
     * Returns ID of group which user belongs to.
     * @return integer
     */
    public function getGroup();

    /**
     * Returns username.
     * @return string
     */
    public function getUsername();

    /**
     * Returns User password
     * @return string
     */
    public function getPassword();

    /**
     * Returns true, if given $password is the same as the User's.
     * @return boolean
     */
    public function passwordMatch($password);
}
