<?php

namespace Fucoso\Site\Interfaces;

use Fucoso\Site\Interfaces\ITemplate;

interface ITemplateCommand
{

    /**
     *
     * @param ITemplate $template
     * @param string $source
     * @param array $data
     */
    public function run(ITemplate $template, $source, array $data = array());
}
