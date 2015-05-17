<?php

namespace Fucoso\Site\Units;

use Fucoso\Site\Interfaces\ICache;

class Cache
{

    /**
     *
     * @var ICache
     */
    protected $provider;

    /**
     *
     * @var boolean
     */
    protected $enabled = false;

    public function __construct(ICache $provider, $adapter = 'file')
    {
        $this->provider = $provider;
        $this->provider->loadDriver($adapter);
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function save($key, $data, $minutesToLive = 60)
    {
        if ($this->enabled) {
            return $this->provider->save($key . '.cache', $data, $minutesToLive);
        }
        return false;
    }

    public function read($key)
    {
        if ($this->enabled) {
            return $this->provider->read($key . '.cache');
        }
        return null;
    }

}
