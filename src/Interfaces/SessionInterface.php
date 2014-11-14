<?php

namespace Fucoso\Site\Interfaces;

interface SessionInterface
{

    public function set_userdata($newdata = array(), $newval = '');

    public function unset_userdata($newdata = array());

    public function userdata($item);
}
