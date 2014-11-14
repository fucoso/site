<?php

namespace Fucoso\Site\Units;

use Exception;
use Fucoso\Site\Site;

class Hold
{

    /**
     *
     * @var Site 
     */
    private $_site = null;

    /**
     *
     * @var string 
     */
    private $_data = array();

    /**
     *
     * @var string 
     */
    private $_sessionPrefix = 'site';

    /**
     * Initiate the Hold Object.
     * 
     * @param Site $site
     */
    public function __construct(Site $site)
    {
        $this->_site = $site;
        $this->_initialize();
        $this->_load();
    }

    public function __destruct()
    {
        $data = serialize($this->_data);
        $this->_setUserData($this->_sessionPrefix . 'holddata', $data);
    }

    private function _initialize()
    {
        
    }

    private function _load()
    {
        if ($this->_site->session) {
            $data = $this->_site->session->userdata($this->_sessionPrefix . 'holddata');

            if ($data && $data != '') {
                try {
                    $unserialized = unserialize($data);
                    if (is_array($unserialized)) {
                        $this->_data = $unserialized;
                    }
                } catch (Exception $e) {
                    
                }
            }
            $this->_site->session->unset_userdata($this->_sessionPrefix . 'holddata');
        }
    }

    private function _setUserData($newdata = array(), $newval = '')
    {
        if ((php_sapi_name() == 'cli') or defined('STDIN')) {
            return;
        }

        if (is_string($newdata)) {
            $newdata = array($newdata => $newval);
        }

        if (count($newdata) > 0) {
            foreach ($newdata as $key => $val) {
                $_SESSION[$key] = $val;
            }
        }
    }

    public function set($key, $value)
    {
        $this->_data[$key] = $value;
        return $this;
    }

    public function get($key = false, $default = null)
    {
        if ($key) {
            if (array_key_exists($key, $this->_data)) {
                return $this->_data[$key];
            } else {
                return $default;
            }
        } else {
            return $this->_data;
        }
    }

    public function __set($key, $value)
    {
        $this->_data[$key] = $value;
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->_data)) {
            return $this->_data[$key];
        } else {
            return null;
        }
    }

    public function reset($key)
    {
        if (array_key_exists($key, $this->_data)) {
            unset($this->_data[$key]);
        }
    }

    public function clear()
    {
        $this->_data = array();
    }

}
