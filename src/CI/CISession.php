<?php

namespace Fucoso\Site\CI;

use Fucoso\Site\Interfaces\ISession;

class CISession implements ISession
{

    public function __construct()
    {

    }

    public function read($key)
    {
        $ci = get_instance();
        if ($this->isReady()) {
            return $ci->session->userdata($key);
        }
    }

    public function remove($key)
    {
        $ci = get_instance();
        if ($this->isReady()) {
            $ci->session->unset_userdata($key);
        }
    }

    public function save($key, $value)
    {
        $ci = get_instance();
        if ($this->isReady()) {
            $ci->session->set_userdata($key, $value);
        }
    }

    public function isReady()
    {
        $ci = get_instance();
        if (isset($ci->session) && $ci->session) {
            return true;
        }
        return false;
    }

}
