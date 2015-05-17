<?php

namespace Fucoso\Site\Units\Template;

use Fucoso\Site\Interfaces\IParser;
use Fucoso\Site\Interfaces\ITemplate;
use Fucoso\Site\Units\Template\Commands\CommandInclude;

class Template implements ITemplate
{

    /**
     *
     * @var string
     */
    protected $theme;

    /**
     *
     * @var string
     */
    protected $themePath;

    /**
     * Layouts and partials will exist in views/layouts but can be set to views/foo/layouts with a verb as foo.
     *
     * @var string
     */
    protected $verb;

    /**
     * By default, dont wrap the view with anything
     *
     * @var string
     */
    protected $layoutView;

    /**
     *
     * @var array
     */
    protected $partials = array();

    /**
     *
     * @var array
     */
    protected $breadcrumbs = array();

    /**
     *
     * @var array
     */
    protected $globalData = array();

    /**
     *
     * @var array[Command]
     */
    protected $commands = array();

    /**
     *
     * @var IParser
     */
    protected $parserProvider;

    function __construct(IParser $parserProvider)
    {
        $this->parserProvider = $parserProvider;

        $this->registerCommands();
    }

    function getParserProvider()
    {
        return $this->parserProvider;
    }

    function getTheme()
    {
        return $this->theme;
    }

    function getThemePath()
    {
        return $this->themePath;
    }

    function getVerb()
    {
        return $this->verb;
    }

    function getLayoutView()
    {
        return $this->layoutView;
    }

    function getPartials()
    {
        return $this->partials;
    }

    function getBreadcrumbs()
    {
        return $this->breadcrumbs;
    }

    function getGlobalData()
    {
        return $this->globalData;
    }

    /**
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->globalData[$key])) {
            return $this->globalData[$key];
        }
        return null;
    }

    /**
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->globalData[$key] = $value;
    }

    /**
     *
     * @param string $theme
     * @return Template
     */
    public function setThemeName($theme = null)
    {
        $this->theme = trim($theme, '/');
        $this->themePath = null;

        if ($theme) {
            if (file_exists($this->parserProvider->getThemesDirectory() . '/' . $theme)) {
                $this->themePath = trim(rtrim($this->parserProvider->getThemesDirectory(), '/') . '/' . $theme . '/');
            }
        }

        return $this;
    }

    /**
     *
     * @param string $layout
     * @return Template
     */
    public function setLayoutView($layout)
    {
        $this->layoutView = $layout;

        return $this;
    }

    public function setVerb($verb)
    {
        $this->verb = $verb;
        return $this;
    }

    /**
     *
     * @param string $key
     * @param string $view
     * @param array $data
     * @return Template
     */
    public function setPartialAsView($key, $view, $data = array())
    {
        $this->partials[$key] = array(
            'view' => $view,
            'data' => $data
        );
        return $this;
    }

    /**
     *
     * @param string $key
     * @param string $string
     * @param array $data
     * @return Template
     */
    public function setPartialAsString($key, $string, $data = array())
    {
        $this->partials[$key] = array(
            'string' => $string,
            'data' => $data
        );
        return $this;
    }

    /**
     *
     * @param string $key
     * @param string $uri
     * @return Template
     */
    public function addBreadCrumb($key, $uri = null)
    {
        $this->breadcrumbs[] = array(
            'key' => $key,
            'uri' => $uri
        );
        return $this;
    }

    protected function addCommand($key, Command $command)
    {
        $this->commands[$key] = $command;
    }

    protected function registerCommands()
    {
        $this->addCommand('include', new CommandInclude());
    }

    /**
     *
     * @param boolean $includeTheme
     * @return string
     */
    protected function getViewsDirectory($includeTheme = true)
    {
        $viewsDirectory = ''; // Starts at the base of the path

        if ($includeTheme && $this->themePath) {
            $viewsDirectory = $this->themePath; // Becomes something like themes/themeName/
        }

        if ($this->verb) {
            $viewsDirectory .= $this->verb . '/'; // Becomes something like views/verb or themes/themeName/verb
        }
        return $viewsDirectory;
    }

    /**
     *
     * @param string $content
     * @param array $data
     * @return string
     */
    public function processCommands($content, array $data = array())
    {
        foreach ($this->commands as $key => $command) {
            /* @var $command Command */
            $content = $command->run($this, $content, $data);
        }
        return $content;
    }

    /**
     *
     * @param string $view
     * @param array $data
     * @return string
     */
    public function viewRaw($view, array $data = array())
    {
        $data['viewsDirectory'] = $this->parserProvider->getViewsVerb() . '/' . $this->getViewsDirectory();

        if ($this->themePath) {

            $themeViewDirectories = $this->parserProvider->getThemeViewDirectories();

            foreach ($themeViewDirectories as $viewDirectory) {

                $viewFile = $this->getViewsDirectory() . rtrim($viewDirectory, '/') . "/{$view}";

                if (file_exists($viewFile . $this->parserProvider->getFileExtension())) {
                    return $this->parserProvider->parseFile($viewFile, $this->globalData + $data, true);
                }
            }
        }
        return $this->parserProvider->parse($this->getViewsDirectory(false) . $view, $this->globalData + $data, true);
    }

    /**
     *
     * @param string $view
     * @param array $data
     * @return string
     */
    public function view($view, array $data = array(), $sendOutput = true)
    {
        $this->prepareBreadcrumbs($data);
        $this->preparePartials($data);

        $viewContent = $this->viewRaw($view, $data);

        if ($this->layoutView) {

            $templateData['innerContent'] = $viewContent;

            $layoutView = rtrim($this->parserProvider->getLayoutsVerb(), '/') . "/{$this->layoutView}";
            $viewContent = $this->viewRaw($layoutView, $templateData + $data);
        }

        $i = 0;
        while (Command::hasCommands($viewContent) && $i++ < 10) {
            $viewContent = $this->processCommands($viewContent, $this->globalData + $data);
        }

        if ($sendOutput) {
            $this->parserProvider->output($viewContent);
        }

        return $viewContent;
    }

    /**
     *
     * @param array $data
     */
    protected function prepareBreadcrumbs(&$data)
    {
        $data['breadcrumbs'] = $this->breadcrumbs;
    }

    /**
     *
     * @param array $data
     */
    protected function preparePartials(&$data)
    {
        $data['partials'] = array();

        foreach ($this->partials as $key => &$partial) {
            if (!is_array($partial['data'])) {
                $partial['data'] = (array) $partial['data'];
            }

            if (isset($partial['view'])) {
                $data['partials'][$key] = $this->viewRaw($partial['view'], $partial['data']);
            } else {
                $partial['string'] = $this->parserProvider->parseString($partial['string'], $this->globalData + $partial['data'], true, true);
                $data['partials'][$key] = $partial['string'];
            }
        }
    }

}
