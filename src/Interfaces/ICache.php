<?php

namespace Fucoso\Site\Interfaces;

interface ICache
{

    /**
     *
     * @param string $adapter
     */
    public function loadDriver($adapter);

    /**
     *
     * @param string $key
     * @param string $value
     * @param string $minutesToLive
     * @return boolean
     */
    public function save($key, $value, $minutesToLive);

    /**
     *
     * @param string $key
     * @return mixed
     */
    public function read($key);

    public function isReady();
}
