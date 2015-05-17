<?php

namespace Fucoso\Site\Units\Template;

class CommandValue
{

    /**
     *
     * @var string
     */
    private $commandName;

    /**
     *
     * @var string
     */
    private $parserTag;

    /**
     *
     * @var string
     */
    private $value;

    public function __construct($commandName, $value, $parserTag)
    {
        $this->commandName = $commandName;
        $this->value = trim($value);
        $this->parserTag = $parserTag;
    }

    function getCommandName()
    {
        return $this->commandName;
    }

    function getParserTag()
    {
        return $this->parserTag;
    }

    function getValue()
    {
        return $this->value;
    }

    function setCommandName($commandName)
    {
        $this->commandName = $commandName;
    }

    function setParserTag($parserTag)
    {
        $this->parserTag = $parserTag;
    }

    function setValue($value)
    {
        $this->value = $value;
    }

}
