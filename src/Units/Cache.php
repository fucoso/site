<?php

namespace Fucoso\Site\Units;

use CI_Cache;
use Fucoso\Site\Site;

class Cache
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
    private $_adapter = 'file';

    /**
     *
     * @var boolean 
     */
    private $_enabled = true;

    /**
     *
     * @var CI_Cache 
     */
    private $_driver = null;

    public function __construct(Site $site)
    {
        $this->_site = $site;

        $this->_enabled = $this->_site->config->cacheEnabled;

        if ($this->_enabled) {

            if ($this->_site->config->cacheAdapter) {
                $this->_adapter = $this->_site->config->cacheAdapter;
            }
            //CodeIgniter Dependent.
            $this->_site->ci->load->driver('cache', array('adapter' => $this->_adapter));
            $this->_driver = $this->_site->ci->cache;
        }
    }

    public function set($key, $data, $ttl = 60)
    {
        if ($this->_enabled && $this->_driver) {
            $this->_driver->save($key . '.cache', $data, $ttl);
            return true;
        } else {
            return false;
        }
    }

    public function get($key)
    {
        if ($this->_enabled && $this->_driver) {
            return $this->_driver->get($key . '.cache');
        } else {
            return null;
        }
    }

}
