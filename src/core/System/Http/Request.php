<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Http;

use System\ParameterBag;

class Request
{
    /**
     * Request body parameters ($_POST)
     *
     * @var \System\ParameterBag
     */
    public $request;

    /**
     * Query string parameters ($_GET)
     *
     * @var \System\ParameterBag
     */
    public $query;

    /**
     * Server and execution environment parameters ($_SERVER)
     *
     * @var \System\Http\ServerBag
     */
    public $server;

    /**
     * Uploaded files ($_FILES)
     *
     * @var \System\Http\FileBag
     * @todo ...
     */
    //public $files;

    /**
     * Cookies ($_COOKIE)
     *
     * @var \System\ParameterBag
     */
    public $cookies;

    /**
     * Headers (taken from the $_SERVER)
     *
     * @var \System\Http\HeaderBag
     */
    public $headers;
    
    /**
     * @var string
     */
    protected $method;
    
    /**
     * @var \System\Http\Session\Session
     */
    protected $session;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $defaultLocale = 'pl';

    /**
     * @var integer
     */
    protected $requestMicrotime = 0;

    /**
     * Constructor.
     *
     * @param array  $query    The GET parameters
     * @param array  $request  The POST parameters
     * @param array  $cookies  The COOKIE parameters
     * @param array  $files    The FILES parameters
     * @param array  $server   The SERVER parameters
     */
    public function __construct(array $query = null, array $request = null, array $cookies = null, array $files = null, array $server = null)
    {
        $this->request    = new ParameterBag($request ? $request : $_POST);
        $this->query      = new ParameterBag($query ? $query : $_GET);
        $this->cookies    = new ParameterBag($cookies ? $cookies : $_COOKIE);
        $this->server     = new ServerBag($server ? $server : $_SERVER);
        $this->headers    = new HeaderBag($this->server->getHeaders());
        //$this->files      = new FileBag($files ? $files : $_FILES);

        $this->requestMicrotime = microtime(true);
    }

    public function getRequestMicrotime()
    {
        return $this->requestMicrotime;
    }

    public function get($key, $default = null)
    {
        if($this !== ($result = $this->query->get($key, $this)))
        {
            return $result;
        }

        if($this !== ($result = $this->request->get($key, $this)))
        {
            return $result;
        }

        return $default;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function hasSession()
    {
        return $this->session !== null;
    }

    public function setSession($session)
    {
        $this->session = $session;

        return $this;
    }

    public function getScriptName()
    {
        return $this->server->get('SCRIPT_NAME', $this->server->get('ORIG_SCRIPT_NAME', ''));
    }

    public function setMethod($method)
    {
        $this->method = $method;
        $this->server->set('REQUEST_METHOD', $method);

        return $this;
    }

    public function getMethod()
    {
        return strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
    }

    public function getContentType()
    {
        return $this->headers->get('CONTENT_TYPE', 'text/plain');
    }

    /**
     * Set the default locale.
     * @return string
     */
    public function setDefaultLocale($locale)
    {
        $this->defaultLocale = $locale;

        return $this;
    }

    /**
     * Get the default locale.
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * Sets the locale.
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->session->set('locale', $this->locale = $locale);

        return $this;
    }

    /**
     * Get the locale.
     * @return string
     */
    public function getLocale()
    {
        return $this->locale == null ? $this->defaultLocale : $this->locale;
    }

    /**
     * Checks if the request method is of specified type.
     * @param string $method Uppercase request method (GET, POST etc).
     * @return bool
     */
    public function isMethod($method)
    {
        return $this->getMethod() === strtoupper($method);
    }

    /**
     * Checks whether the method is safe or not.
     * @return bool
     */
    public function isMethodSafe()
    {
        return in_array($this->getMethod(), array('GET', 'HEAD'));
    }

    /**
     * Returns true if the request is a XMLHttpRequest.
     *
     * It works if your JavaScript library sets an X-Requested-With HTTP header.
     * It is known to work with common JavaScript frameworks:
     * @link http://en.wikipedia.org/wiki/List_of_Ajax_frameworks#JavaScript
     * @return bool true if the request is an XMLHttpRequest, false otherwise
     */
    public function isXmlHttpRequest()
    {
        return 'XMLHttpRequest' == $this->headers->get('X-Requested-With');
    }

    /**
     * Alias to isXmlHttpRequest.
     */
    public function isAJAX()
    {
        return $this->isXmlHttpRequest();
    }

    /**
     * Returns absolute path of URL, without Query String.
     * @return string
     */
    public function getBasePath()
    {
        return str_replace('index.php', '', $this->server->get('SCRIPT_NAME'));
    }

    /**
     * Returns path of URL, without Query String.
     * @return string
     */
    public function getPath()
    {
        return explode('?', $this->server->get('REQUEST_URI'))[0];
    }

    public function getUriForPath($path)
    {
        return 'http://'.$this->server->get('SERVER_NAME').rtrim($this->getBasePath(), '/').'/'.ltrim($path, '/');
    }

    /**
     * Returns full URL of current Request.
     * @return string Full URL.
     */
    public function getFullUrl()
    {
        return 'http'.(isset($_SERVER['HTTPS']) ? 's' : '')."://{$this->server->get('SERVER_NAME')}{$this->server->get('REQUEST_URI', '/')}";
    }

    /**
     * Parses QyeryString and returns array.
     * 
     * @return array Array of values in Query String.
     */
    public function getQueryArray()
    {
        parse_str($this->server->get('QUERY_STRING', ''), $query);
        return $query;
    }

    /**
     * Parses URL and returns its segments as Array.
     * @return array Array of segments.
     */
    public function getUriSegments($query = null)
    {
        $segments = parse_url($this->getFullUrl());

        if(isset($segments['query']))
        {
            parse_str($segments['query'], $segments['query']);
        }
        else
        {
            $segments['query'] = [];
        }

        return $segments;
    }

    /**
     * Builds Full URI from given segments. If some segments not exists, will be
     * fill from current URL segments.
     * @param array   $segments    Array of segment from which URL will be build.
     * @param boolean $removeQuery Append values or replace QueryString? If true,
     *                             QS will be replaced by given.
     * @return string URL string.
     */
    public function buildUriFromSegments(array $segments = [], $removeQuery = false)
    {
        $segments = array_merge([
            'scheme' => 'http'.(isset($_SERVER['HTTPS']) ? 's' : ''),
            'host'   => $this->server->get('SERVER_NAME'),
            'path'   => $this->getPath(),
            'query'  => []
        ], $segments);

        if($removeQuery === false)
        {
            $segments['query'] = array_merge($this->getQueryArray(), $segments['query']);
        }

        return "{$segments['scheme']}://{$segments['host']}{$segments['path']}?".http_build_query($segments['query']);
    }
}
