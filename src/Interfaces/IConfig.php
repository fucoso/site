<?php

namespace Fucoso\Site\Interfaces;

interface IConfig
{

    /**
     *
     * @param string $group
     * @return array
     */
    public function loadFromGroup($group);
}
