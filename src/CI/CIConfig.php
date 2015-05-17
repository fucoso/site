<?php

namespace Fucoso\Site\CI;

use Fucoso\Site\Interfaces\IConfig;

class CIConfig implements IConfig
{

    public function __construct()
    {

    }

    /**
     *
     * @param string $group
     * @return array
     */
    public function loadFromGroup($group)
    {
        $ci = get_instance();

        $results = array();
        if (isset($ci->config) && $ci->config && $ci->config->load($group, true, true)) {
            $results = $ci->config->item($group);
        }
        return $results;
    }

}
