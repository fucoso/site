<?php

namespace Fucoso\Site\Units\Template\Commands;

use Fucoso\Site\Interfaces\ITemplate;
use Fucoso\Site\Units\Template\Command;
use Fucoso\Site\Units\Template\CommandValue;

class CommandInclude extends Command
{

    /**
     *
     * @var string
     */
    protected $name = 'include';

    /**
     *
     * @param CommandValue $commandValue
     * @param ITemplate $template
     * @param string $content
     * @param array $data
     */
    protected function parseSingleCommand(CommandValue $commandValue, ITemplate $template, array $data = array())
    {
        return $template->viewRaw($commandValue->getValue(), $data);
    }

}
