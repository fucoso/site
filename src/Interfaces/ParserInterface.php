<?php

namespace Fucoso\Site\Interfaces;

interface ParserInterface
{

    function parse($template, $data, $return = FALSE);
}
