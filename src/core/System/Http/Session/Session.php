<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace System\Http\Session;

use System\Database\Database;
use System\Http\Request;

class Session
{
    use \System\Utils\ArrayIndexTranslateTrait;

    private $id;
    private $owner = 0;

    private $db;

    private $request;

    private $lifetime = 7200;
    private $isNewSession = false;
    private $removedSessions = [];

    private $content = [];

    private $bags = [];

    public function __construct(Database $db, Request $request)
    {
        $this->db         = $db;
        $this->request    = $request;
    }

    public function start()
    {
        $server = $this->request->server;

        $now        = time();
        $userAgent  = $server->get('HTTP_USER_AGENT', 'Verone-CRM/1.0 PHP/5.4+');
        $userIp     = $server->get('REMOTE_ADDR', '127.0.0.1');
        $serverName = $server->get('SERVER_NAME', 'unknown.veronecrm.com');

        // Save old sessions. We dont wan't to get session content, because
        // it's to heavy, and also we don't need it anyway.
        $this->removedSessions = $this->db->query('SELECT id, owner, last_action FROM #__session WHERE last_action <= '.($now - $this->lifetime));

        // Delete old sessions
        $this->db->exec('DELETE FROM #__session WHERE last_action <= '.($now - $this->lifetime));

        session_name('verone-crm');
        // @todo Check if this works. Test.
        // session_set_cookie_params(0);
        session_set_cookie_params($this->lifetime, '/', $serverName, false, true);
        session_start();

        /**
         * If session index not exists and UA string in session is different than current,
         * we create new session.
         */
        if(isset($_SESSION['SERV_UA']) && $_SESSION['SERV_UA'] != md5($userAgent))
        {
            $this->regenerate();
        }
        else
        {
            $_SESSION['SERV_UA'] = md5($userAgent);
        }

        // We store Last Action time in session variable
        if(! isset($_SESSION['USER_LAST_ACTION']))
        {
            $_SESSION['USER_LAST_ACTION'] = $now;
        }
        // If session time is ended, we regenerate session.
        elseif((int) $_SESSION['USER_LAST_ACTION'] + $this->lifetime < $now)
        {
            // We don't need cookie anymore...
            if(isset($_COOKIE['verone-crm']))
            {
                setcookie('verone-crm', '', $now - $this->lifetime, '/', $serverName, false, false);
            }

            $this->regenerate();
        }
        // Else, we create new cookie and update user Last Action.
        else
        {
            $_SESSION['USER_LAST_ACTION'] = $now;

            if(isset($_COOKIE['verone-crm']))
            {
                setcookie('verone-crm', $_COOKIE['verone-crm'], $now + $this->lifetime, '/', $serverName, false, false);
            }
        }

        $this->id = session_id();

        // We selecting session row from DB
        $session = $this->db->query("SELECT * 
            FROM #__session
            WHERE id = '{$this->id}'
            LIMIT 1");

        // If we don't have session in DB, we create one.
        if($session === [])
        {
            $this->db->exec("INSERT INTO #__session (id, last_action, content) VALUES  ('{$this->id}', {$now}, '".$this->toStore([])."')");

            $this->isNewSession = true;
        }
        else
        {
            $this->content = $this->fromStore($session[0]['content']);
            $this->owner   = $session[0]['owner'];
        }

        $this->updateDataInBags();
    }

    public function isNewSession()
    {
        return $this->isNewSession;
    }

    public function getRemovedSessions()
    {
        return $this->removedSessions;
    }

    public function save()
    {
        $this->db->exec("UPDATE #__session SET content = '".$this->toStore($this->content)."', last_action = ".time().", id = '{$this->id}', owner = '{$this->owner}' WHERE id = '{$this->id}'");
        
        session_write_close();

        return $this;
    }

    public function all()
    {
        return $this->content;
    }

    public function has($name = null)
    {
        return $this->existsInArray($name, $this->content);
    }

    public function get($name, $default = null)
    {
        if($name === null)
            return $this->content;
        else
            return $this->getFromArray($name, $this->content);
    }

    public function set($name, $value)
    {
        $this->content = $this->setInArray($name, $value, $this->content);

        return $this;
    }

    public function replace(array $attributes)
    {
        return $this;
    }

    public function remove($name)
    {
        $this->content = $this->removeFromArray($name, $this->content);

        return $this;
    }

    public function clear()
    {
        $this->session = array();

        return $this;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getBag($name)
    {
        return isset($this->bags[$name]) ? $this->bags[$name] : null;
    }

    public function setBag($name, $bag)
    {
        $this->bags[$name] = $bag;

        $this->updateDataInBags();

        return $this;
    }

    public function getFlashBag()
    {
        return $this->getBag('flashes');
    }

    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function toStore($content = null)
    {
        return base64_encode(serialize($content ? $content : $this->content));
    }

    public function fromStore($content = null)
    {
        return unserialize(base64_decode($content ? $content : $this->content));
    }
    
    public function regenerate()
    {
        $_SESSION = array();
        session_destroy();
        session_start(); 
        session_regenerate_id(true);

        return $this;
    }

    private function updateDataInBags()
    {
        if(! isset($this->content['__bags_content']))
        {
            $this->content['__bags_content'] = array();
        }

        foreach($this->bags as $name => $bag)
        {
            if(! isset($this->content['__bags_content'][$name]))
            {
                $this->content['__bags_content'][$name] = [];
            }
        }

        foreach($this->content['__bags_content'] as $name => &$data)
        {
            if(isset($this->bags[$name]))
            {
                $this->bags[$name]->initialize($data);
            }
        }
    }
}
