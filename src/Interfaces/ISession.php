<?php

namespace Fucoso\Site\Interfaces;

interface ISession
{

    /**
     *
     * @param string $key
     * @param string $value
     */
    public function save($key, $value);

    /**
     *
     * @param string $key
     */
    public function remove($key);

    /**
     *
     * @param string $key
     * @return mixed
     */
    public function read($key);

    /**
     *
     * @return boolean
     */
    public function isReady();
}
