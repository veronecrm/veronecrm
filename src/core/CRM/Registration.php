<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM;

use System\DependencyInjection\Container;

class Registration
{
    /**
     * Registration check URL.
     * @var string
     */
    protected $checkUrl = 'http://api.registration.veronecrm.com/0.1/check';

    /**
     * Registration check cache.
     * @var boolean
     */
    protected $isRegistered = null;

    /**
     * Application ID to check.
     * @var string
     */
    protected $appId;

    protected $settings;

    public function __construct(Container $container)
    {
        $this->settings = $container->get('settings')->open('app', null);

        $this->appId = $this->settings->get('id');

        $lastCheck = (int) $this->settings->get('registration.lastcheck');

        if(date('dmY', $lastCheck) != date('dmY'))
        {
            $this->isRegistered = null;
        }
        else
        {
            $this->isRegistered = (boolean) $this->settings->get('registration.status');
        }
    }

    /**
     * Set Application ID to check.
     * @param  string $appId
     * @return self
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;

        return this;
    }

    /**
     * Return stored Application ID.
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * Check, if given $appId is registered on current domain.
     * Create request every time is called.
     * @param  string $appId Application ID to check.
     * @return boolean
     */
    public function check($appId = null)
    {
        if($appId == null)
        {
            $appId = $this->appId;
        }

        $s = curl_init();

        curl_setopt($s, CURLOPT_URL, $this->checkUrl);
        curl_setopt($s, CURLOPT_TIMEOUT, 5);
        curl_setopt($s, CURLOPT_MAXREDIRS, 10);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($s, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($s, CURLOPT_POST, true);
        curl_setopt($s, CURLOPT_POSTFIELDS, http_build_query([ 'app-id' => $appId, 'domain' => isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'not-provided' ]));

        curl_setopt($s, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)');

        $result = curl_exec($s);
        @ $data = json_decode($result, true);

        curl_close($s);

        if(json_last_error() == JSON_ERROR_NONE && isset($data['check']))
        {
            return $data['check'];
        }

        return false;
    }

    /**
     * Returns info that application is registered.
     * Create request only once per day.
     * @return boolean
     */
    public function isRegistered()
    {
        if($this->isRegistered !== null)
        {
            return $this->isRegistered;
        }

        $this->isRegistered = $this->check($this->appId);

        $this->settings->set('registration.lastcheck', time());
        $this->settings->set('registration.status', $this->isRegistered);

        return $this->isRegistered;
    }
}
