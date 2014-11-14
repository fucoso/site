<?php

namespace Fucoso\Site\Units;

use Exception;
use Fucoso\Site\Site;

class Next
{

    /**
     *
     * @var Site 
     */
    private $_site = null;
    private $_data = array();
    private $_oldData = array();
    private $_sessionPrefix = 'site';

    /**
     * Initiate the Next Object.
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
        $this->_setUserData($this->_sessionPrefix . 'nextdata', $data);
    }

    private function _initialize()
    {
        
    }

    private function _load()
    {
        if ($this->_site->session) {
            $data = $this->_site->session->userdata($this->_sessionPrefix . 'nextdata');

            if ($data && $data != '') {
                try {
                    $old_data = unserialize($data);
                    if (is_array($old_data)) {
                        $this->_oldData = $old_data;
                    }
                } catch (Exception $e) {
                    
                }
            }
            $this->_site->session->unset_userdata($this->_sessionPrefix . 'nextdata');
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
            if (array_key_exists($key, $this->_oldData)) {
                return $this->_oldData[$key];
            } else {
                return $default;
            }
        } else {
            return $this->_oldData;
        }
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->_oldData)) {
            return $this->_oldData[$key];
        } else {
            return null;
        }
    }

    public function propagate($key)
    {
        if ($this->get($key, false)) {
            $this->set($key, $this->get($key));
        }
    }

    public function clear()
    {
        $this->_data = array();
    }

}
