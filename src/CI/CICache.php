<?php

namespace Fucoso\Site\CI;

use Fucoso\Site\Interfaces\ICache;

class CICache implements ICache
{

    /**
     *
     * @var \CI_Cache
     */
    private $ciCache;

    /**
     *
     * @var boolean
     */
    private $loaded = false;

    public function __construct()
    {

    }

    public function isReady()
    {
        return $this->loaded;
    }

    public function loadDriver($adapter)
    {
        if ($adapter) {
            $ci = get_instance();
            $ci->load->driver('cache', array('adapter' => $adapter));
            $this->ciCache = $ci->cache;
            $this->loaded = true;
        }
    }

    public function save($key, $value, $minutesToLive)
    {
        if ($this->isReady()) {
            $this->ciCache->save($key, $value, $minutesToLive);
            return true;
        }
        return false;
    }

    public function read($key)
    {
        if ($this->isReady()) {
            return $this->cacheDriver->get($key);
        }
        return null;
    }

}
