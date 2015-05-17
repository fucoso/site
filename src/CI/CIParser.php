<?php

namespace Fucoso\Site\CI;

use Fucoso\Site\Interfaces\IParser;

class CIParser implements IParser
{

    /**
     *
     * @var string
     */
    private $module = '';

    /**
     *
     * @var string
     */
    private $controller = '';

    /**
     *
     * @var string
     */
    private $method = '';

    /**
     *
     * @var string
     */
    private $layoutsVerb = 'layouts';

    /**
     *
     * @var string
     */
    private $viewsVerb = 'views';

    /**
     *
     * @var string
     */
    private $modulesVerb = 'modules';

    /**
     *
     * @var string
     */
    private $themesDirectory = 'themes';

    public function __construct()
    {
        $this->themesDirectory = APPPATH . $this->themesDirectory;
    }

    public function load()
    {
        $ci = get_instance();

        $ci->load->library('parser');

        // Modular Separation / Modular Extensions has been detected
        if (method_exists($ci->router, 'fetch_module')) {
            $this->module = $ci->router->fetch_module();
        }

        // What controllers or methods are in use
        $this->controller = $ci->router->fetch_class();
        $this->method = $ci->router->fetch_method();
    }

    public function isReady()
    {
        $ci = get_instance();
        if (isset($ci->parser) && $ci->parser) {
            return true;
        }
        return false;
    }

    public function parse($view, $viewData, $returnOutput = false)
    {
        $ci = get_instance();
        if ($this->isReady()) {
            return $ci->parser->parse($view, $viewData, $returnOutput);
        }
        return null;
    }

    public function parseString($string, $viewData, $returnOutput = false)
    {
        $ci = get_instance();
        if ($this->isReady()) {
            return $ci->parser->parse_string($string, $viewData, $returnOutput);
        }
        return null;
    }

    public function parseFile($file, $viewData, $returnOutput = false)
    {
        $ci = get_instance();
        if ($this->isReady()) {
            $ci->load->vars($viewData);
            $content = $ci->load->file($file . $this->getFileExtension(), true);
            return $ci->parser->parse_string($content, $viewData, $returnOutput);
        }
        return null;
    }

    public function getFileExtension()
    {
        return '.php';
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getLayoutsVerb()
    {
        return $this->layoutsVerb;
    }

    public function getViewsVerb()
    {
        return $this->viewsVerb;
    }

    public function getModulesVerb()
    {
        return $this->modulesVerb;
    }

    public function getThemesDirectory()
    {
        return $this->themesDirectory;
    }

    public function getThemeViewDirectories()
    {
        $viewPaths = array();

        if ($this->getModule()) {
            /*
             * Modules folder would reside inside theme directory and module name folder inside that folder.
             */
            $viewPaths[] = "{$this->getModulesVerb()}/{$this->getModule()}";
        }

        /*
         * Directly inside the theme directory, to avoid having to create a views directory inside theme.
         */
        $viewPaths[] = "";

        return $viewPaths;
    }

    public function output($content)
    {
        echo $content;
    }

}
