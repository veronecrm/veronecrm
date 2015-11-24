<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Settings;

use System\Settings\Cache;
use System\Http\Session\Session;

class SessionCache extends Cache
{
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key, $name, $type, $param)
    {
        return $this->session->has("settings.{$key}.{$name}.{$type}.{$param}");
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $name, $type, $param)
    {
        return $this->session->get("settings.{$key}.{$name}.{$type}.{$param}");
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $name, $type, $param, $value)
    {
        $this->session->set("settings.{$key}.{$name}.{$type}.{$param}", $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function retrieve()
    {

    }
}
