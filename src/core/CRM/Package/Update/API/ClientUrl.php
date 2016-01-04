<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Package\Update\API;

class ClientUrl
{
    protected $url    = '';
    protected $params = [];

    protected $body   = '';
    protected $status = 200;

    public function __construct($url, array $params = [])
    {
        $this->url    = $url;
        $this->params = $params;
    }

    public function call()
    {
        $s = curl_init();

        curl_setopt($s, CURLOPT_URL, $this->url);
        curl_setopt($s, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($s, CURLOPT_TIMEOUT, 5);
        curl_setopt($s, CURLOPT_MAXREDIRS, 10);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($s, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($s, CURLOPT_POST, true);
        curl_setopt($s, CURLOPT_POSTFIELDS, http_build_query($this->params));

        curl_setopt($s, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)');

        $this->body   = curl_exec($s);
        $this->status = curl_getinfo($s, CURLINFO_HTTP_CODE);

        curl_close($s);
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
