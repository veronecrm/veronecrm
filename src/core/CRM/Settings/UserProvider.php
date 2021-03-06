<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Settings;

use System\Settings\Provider;

class UserProvider extends Provider
{
    /**
     * {@inheritdoc}
     */
    protected $type = 2;

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        /**
         * First, search for key
         */
        $key = $this->db->query("SELECT id
            FROM #__setting_key
            WHERE (
                    `key` = '{$name}'
                AND `type` = '{$this->type}'
            )
            LIMIT 1
            ");

        if(isset($key[0]['id']))
        {
            /**
             * Second, search for value.
             */
            $value = $this->db->query("SELECT id
                FROM #__setting_value
                WHERE (
                        `key` = '{$key[0]['id']}'
                    AND `param` = '{$this->param}'
                )
                LIMIT 1
                ");

            if(isset($value[0]['id']))
            {
                return $value[0]['id'];
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, $default = null)
    {
        if($this->cache->has('app', $name, $this->type, $this->param))
        {
            return $this->cache->get('app', $name, $this->type, $this->param);
        }

        $result = $this->db->query("SELECT v.`value`
            FROM #__setting_key k
            INNER JOIN #__setting_value v
            ON (
                k.`id` = v.`key`
            )
            WHERE (
                    k.`key` = '{$name}'
                AND k.`type` = {$this->type}
                AND v.`param` = '{$this->param}'
            )
            LIMIT 1");

        if($result == array())
        {
            $this->cache->set('app', $name, $this->type, $this->param, $default);

            return $default;
        }
        else
        {
            $this->cache->set('app', $name, $this->type, $this->param, $result[0]['value']);

            return $result[0]['value'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value)
    {
        $id = $this->has($name);

        if($id === false)
        {
            return false;
        }

        $this->cache->set('app', $name, $this->type, $this->param, $value);

        $this->db->exec("UPDATE #__setting_value
            SET value = '{$value}'
            WHERE id = {$id}");

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function registerKey($name, $default)
    {
        $key = $this->db->query("SELECT id
            FROM #__setting_key
            WHERE (
                    `key` = '{$name}'
                AND `type` = '{$this->type}'
            )
            LIMIT 1");

        if(isset($key[0]['id']))
        {
            return false;
        }


        $this->db->exec("INSERT INTO #__setting_key
                ( `key`, `default`, `type` )
            VALUES
                ( '{$name}', '{$default}', {$this->type} )");



        $key = $this->db->query("SELECT id
            FROM #__setting_key
            WHERE (
                    `key` = '{$name}'
                AND `type` = '{$this->type}'
            )
            LIMIT 1");

        if(! isset($key[0]['id']))
        {
            return false;
        }



        $users = $this->db->query("SELECT id FROM #__user");

        if(is_array($users))
        {
            /**
             * Register value for every user in DB.
             */
            foreach($users as $user)
            {
                $this->db->exec("INSERT INTO #__setting_value
                        ( `key`, `value`, `param` )
                    VALUES
                        ( '{$key[0]['id']}', '{$default}', {$user['id']} )");
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function unregisterKey($name)
    {
        $key = $this->db->query("SELECT id
            FROM #__setting_key
            WHERE (
                    `key` = '{$name}'
                AND `type` = '{$this->type}'
            )
            LIMIT 1
            ");

        if(! isset($key[0]['id']))
        {
            return false;
        }

        $this->db->exec("DELETE FROM #__setting_key
            WHERE
                    `key` = '{$name}'
                AND `type` = '{$this->type}'
            ");

        $users = $this->db->query("SELECT id FROM #__user");

        if(is_array($users))
        {
            /**
             * Remove value for every user in DB.
             */
            foreach($users as $user)
            {
                $this->db->exec("DELETE FROM #__setting_value
                    WHERE
                            `key` = '{$key[0]['id']}'
                        AND `param` = '{$user['id']}'
                    ");
            }
        }

        return true;
    }

    public function onUserAdd($user)
    {
        /**
         * Find all keys in DB
         */
        $keys = $this->db->query("SELECT *
            FROM #__setting_key
            WHERE (
                `type` = '{$this->type}'
            )");

        // Save values for keys for user with default values
        foreach($keys as $key)
        {
            $this->db->exec("INSERT INTO #__setting_value
                    ( `key`, `value`, `param` )
                VALUES
                    ( '{$key['id']}', '{$key['default']}', '".$user->getId()."' )");
        }
    }

    public function onUserDelete($user)
    {
        /**
         * Find all keys in DB
         */
        $keys = $this->db->query("SELECT *
            FROM #__setting_key
            WHERE (
                `type` = '{$this->type}'
            )");

        // Delete values for this user.
        foreach($keys as $key)
        {
            $this->db->exec("DELETE FROM #__setting_value
                WHERE 
                        `key` = {$key['id']}
                    AND `param` = ".$user->getId());
        }
    }
}
