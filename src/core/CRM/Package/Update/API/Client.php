<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Package\Update\API;

class Client
{
    /**
     * ID of Application from which we call API.
     * @var string
     */
    protected $appId;

    /**
     * Version of application (SemVer) for which we check packages.
     * @var string
     */
    protected $appVersion;

    /**
     * Base URL to server of update API.
     * @var string
     */
    protected $apiUrl = '';

    /**
     * Version of API we use.
     * @var string
     */
    protected $apiVersion = '1.0';

    /**
     * language that API Server should respect for messages.
     * @var string
     */
    protected $language = 'pl';

    /**
     * SessionID assigned from Update Server while session initiated.
     * @var string
     */
    protected $sessionId = null;

    /**
     * Stores last cURL object.
     * @var ClientURL
     */
    protected $lastCurl = null;

    /**
     * Construct.
     * @param string $appId      Application API from which we call API.
     * @param string $appVersion Version of application (SemVer) for which we check packages.
     * @param string $apiUrl     Server URL to connect to.
     */
    public function __construct($appId, $appVersion, $apiUrl)
    {
        $this->appId      = $appId;
        $this->appVersion = $appVersion;
        $this->apiUrl     = $apiUrl;
    }

    /**
     * Allows to set Session ID retrived before.
     * Remember! Session ID lifetime is one hour!
     * @param string $id Session ID.
     * @return self
     */
    public function setSessionId($id)
    {
        $this->sessionId = $id;

        return $this;
    }

    /**
     * Returns Session ID for future usage.
     * @return string Session ID.
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Check if given packages have some updates. $packages variable should store
     * arrays with followed indexes:
     *  - package  - Package ID.
     *  - version - Current version of Package.
     * @param  array  $packages Array of packages to check.
     * @return array           Array of packages details.
     */
    public function checkAvailability(array $packages)
    {
        if($this->sessionId === null)
        {
            throw new \Exception('SessionID of API is empty. Init session to call API.');
        }

        return $this->call('/check/availability', [
            'packages' => $packages
        ]);
    }

    /**
     * Download package by given parameters, and save file in $destination.
     * @param  string $package      Package ID.
     * @param  string $version     Version (SemVer) of downloaded file.
     * @param  string $destination Path to destination directory.
     * @return string              Path to saved file.
     */
    public function downloadPackage($package, $version, $destination)
    {
        $result = $this->call('/package/download', [
            'package'  => $package,
            'version' => $version,
        ], false);

        // Server must return 200 response status
        if($this->lastCurl->getStatus() == 400)
        {
            return false;
        }

        // We create destination if doesnt exists.
        if(is_dir($destination) === false)
        {
            mkdir($destination, 0777, true);
        }

        $filepath = "{$destination}/{$package}-{$version}.zip";

        // Save package and return path to file.
        if(file_put_contents($filepath, $result) === false)
        {
            throw new \Exception('Cannot save package in given destination.');
        }

        return $filepath;
    }

    public function initSession()
    {
        $result = $this->call('/session/init', [
            'app-id'      => $this->appId,
            'app-version' => $this->appVersion,
            'language'    => $this->language
        ]);

        if(isset($result['id']))
        {
            $this->sessionId = $result['id'];
        }
        else
        {
            throw new \Exception('Session not initiated. Update server respond Error.');
        }
    }

    public function call($api, array $params = [], $parseResponse = true)
    {
        // Append SessionId for 
        $params['session-id'] = $this->sessionId;

        $this->lastCurl = new ClientUrl($this->apiUrl.$this->apiVersion.$api, $params);
        $this->lastCurl->call();

        /**
         * Pobrać body i przeparsować z JSONa,
         * obsłużyć błędy i zwrócić obiekt Response.
         */
        // We parse response as JSON.
        if($parseResponse)
        {
            /**
             * @todo Check what result give json_docode and throw exception
             *       with value from json_last_error() : http://php.net/manual/en/function.json-last-error.php
             */
            return json_decode($this->lastCurl->getBody(), true);
        }
        // Otherwise we return response untouched
        else
        {
            return $this->lastCurl->getBody();
        }
    }
}
