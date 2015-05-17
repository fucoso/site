<?php

namespace Fucoso\Site\Units\Template;

use Fucoso\Site\Interfaces\ITemplate;
use Fucoso\Site\Interfaces\ITemplateCommand;

abstract class Command implements ITemplateCommand
{

    /**
     *
     * @var string
     */
    protected $name;

    public function __construct()
    {

    }

    public static function hasCommands($content)
    {
        $regexPattern = "/\{\:.+\:(.*?)\}/";
        if (preg_match($regexPattern, $content)) {
            return true;
        }
        return false;
    }

    public function run(ITemplate $template, $source, array $data = array())
    {
        $commands = $this->getCommands($source);
        if (count($commands) > 0) {
            $source = $this->traverseCommands($commands, $template, $source, $data);
        }
        return $source;
    }

    protected function getCommands($source)
    {
        $commands = array();

        $regexPattern = "/\{\:{$this->name}\:(.*?)\}/";

        $matches = array();
        preg_match_all($regexPattern, $source, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            if (!array_key_exists($match[0], $commands)) {
                $commands[$match[0] . ''] = new CommandValue($this->name, $match[1], $match[0]);
            }
        }
        return $commands;
    }

    protected function traverseCommands($commands, ITemplate $template, $source, array $data = array())
    {
        foreach ($commands as $command) {
            /* @var $command CommandValue */
            $parsedCommand = $this->parseSingleCommand($command, $template, $data);
            $source = str_replace($command->getParserTag(), $parsedCommand, $source);
        }
        return $source;
    }

    /**
     *
     * @param string $parserTag
     * @param CommandValue $commandValue
     * @param ITemplate $template
     * @param string $content
     * @param array $data
     * @return string
     */
    abstract protected function parseSingleCommand(CommandValue $commandValue, ITemplate $template, array $data = array());
}
