<?php
/**
 * Verone CRM | http://www.veronecrm.com
 *
 * @copyright  Copyright (C) 2015 - 2016 Adam Banaszkiewicz
 * @license    GNU General Public License version 3; see license.txt
 */

namespace CRM\Settings;

use System\Settings\Provider;

class AppProvider extends Provider
{
    /**
     * {@inheritdoc}
     */
    protected $type = 0;

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

        $result = $this->db->query("SELECT v.value
            FROM #__setting_key k
            INNER JOIN #__setting_value v
            ON (
                k.`id` = v.`key`
            )
            WHERE (
                    k.`key` = '{$name}'
                AND k.`type` = {$this->type}
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
            LIMIT 1
            ");

        if(isset($key[0]['id']))
        {
            return false;
        }

        $this->db->exec("INSERT INTO #__setting_key
                ( `key`, `default`, `type` )
            VALUES
                ( '{$name}', '{$default}', '{$this->type}' )");

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

        $this->db->exec("INSERT INTO #__setting_value
                ( `key`, `value`, `param` )
            VALUES
                ( '{$key[0]['id']}', '{$default}', '{$this->param}' )");

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

        $this->db->exec("DELETE FROM #__setting_value
            WHERE `key` = '{$key[0]['id']}'
            ");

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function optimizeValues()
    {
        
    }
}
