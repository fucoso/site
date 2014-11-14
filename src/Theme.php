<?php

namespace Fucoso\Site;

use Exception;
use Fucoso\Site\Request\Request;

/**
 *
 */
class Theme
{

    const NEW_LINE = "\n";

    /**
     *
     * @var Site
     */
    private $_site = null;

    /**
     *
     * @var string
     */
    private $_theme = '';

    /**
     *
     * @var string
     */
    private $_themeVerb = '';

    /**
     *
     * @var string
     */
    private $_themeMedia = '';

    /**
     *
     * @var string
     */
    private $_themeTemplate = '';

    /**
     *
     * @var array
     */
    private $_globalData = array();

    /**
     *
     * @var array
     */
    private $_data = array();

    /**
     *
     * @var array
     */
    private $_partials = array();

    /**
     *
     * @var array
     */
    private $_commands = array();

    /**
     *
     * @var boolean
     */
    private $_useMedia = true;

    public function __construct(Site $site)
    {
        $this->_site = $site;

        $this->_initialize();
    }

    private function _initialize()
    {
        $this->_themeMedia = $this->_site->request->media;
    }

    private function _adjustThemeMedia()
    {
        if ($this->getUseMedia()) {
            // Make sure that the directory for current media exists & if it doesn't then fallback to
            // web media.
            $this->_themeMedia = $this->_site->request->media;
            if (!is_dir($this->_getPathInsideTheme())) {
                $this->_themeMedia = Request::MEDIA_WEB;
            }
        } else {
            $this->_themeMedia = '';
        }
    }

    public function setTheme($theme)
    {
        if ($theme) {
            if (is_dir(APPPATH . "views/themes/{$theme}")) {
                $this->_theme = $theme;
                $this->_themeVerb = '';
                $this->_adjustThemeMedia();
            } else {
                throw new Exception("Provided Theme '{$theme}' Does Not Exist.");
            }
        } else {
            throw new Exception("Theme Name Not Provided.");
        }
    }

    public function setThemeVerb($themeVerb)
    {
        if ($this->_theme) {
            if ($themeVerb) {
                if (is_dir(APPPATH . "views/themes/{$this->_theme}/{$themeVerb}")) {
                    $this->_themeVerb = $themeVerb;
                    $this->_adjustThemeMedia();
                } else {
                    throw new Exception("Provided Theme Verb '{$themeVerb}' Does Not Exist in Theme '{$this->_theme}'.");
                }
            } else {
                throw new Exception("Theme Name Not Provided.");
            }
        } else {
            throw new Exception("Please Set Theme Before Setting Theme Verb.");
        }
    }

    public function setTemplate($template)
    {
        if ($this->_theme) {
            if ($template) {
                if (file_exists($this->_getPathInsideTheme() . "templates/{$template}.php")) {
                    $this->_themeTemplate = $template;
                } else {
                    throw new Exception("Provided Template '{$template}' Does Not Exist in Theme Path '" . $this->_getPathInsideTheme(false) . "templates/{$this->_themeMedia}'.");
                }
            } else {
                throw new Exception("Theme Name Not Provided.");
            }
        } else {
            throw new Exception("Please Set Theme Before Setting Theme Verb.");
        }
    }

    private function _getPathInsideTheme($addBasePath = true)
    {
        if ($this->_theme) {

            $mediaString = '';
            $verbString = '';

            if ($this->getUseMedia()) {
                $mediaString = "{$this->_themeMedia}/";
            }

            if ($this->_themeVerb) {
                $verbString = "{$this->_themeVerb}/";
            }

            return (($addBasePath) ? APPPATH . "views/" : "") . "themes/{$this->_theme}/$verbString$mediaString";
        } else {
            return false;
        }
    }

    public function getPathInsideTheme($addBasePath = true)
    {
        return $this->_getPathInsideTheme($addBasePath);
    }

    private function _getTemplatePath($addBasePath = true)
    {
        return $this->_getPathInsideTheme($addBasePath) . "templates/" . $this->_themeTemplate;
    }

    public function single($page, $pageData = NULL, $return = FALSE)
    {
        if (!$this->_theme) {
            throw new Exception("No Theme has been set.");
        }

        return $this->_rawViewInternal($page, $pageData, $return);
    }

    public function singlePage($page, $pageData = NULL, $return = FALSE)
    {
        if (!$this->_theme) {
            throw new Exception("No Theme has been set.");
        }

        return $this->_rawViewInternal('pages/' . $page, $pageData, $return);
    }

    private function _rawViewInternal($page, $pageData = NULL, $return = FALSE)
    {
        $this->_data = array();

        if ($pageData && is_array($pageData)) {
            $this->_data = $pageData + $this->_globalData;
        } else {
            $this->_data = $this->_globalData;
        }

        $this->_fillTemplateData($page);
        $this->_loadGlobalProcessor();
        $this->_loadGlobalConfiguration();

        $this->_rawParseSubPage();
        $this->_rawParsePage($page);

        $this->_renderCommands($this->_data['pageContent']);

        if ($return) {
            return $this->_data['pageContent'];
        } else {
            echo $this->_data['pageContent'];
        }
    }

    public function view($page, $pageData = NULL, $return = FALSE)
    {
        if (!$this->_theme) {
            throw new Exception("No Theme has been set.");
        }

        return $this->_viewInternal($page, $pageData, $return);
    }

    private function _viewInternal($page, $pageData = NULL, $return = FALSE)
    {
        $this->_data = array();
        /*
         * Here we verify that if the page is existent in the given target.
         */
        $this->_pageExists($page);

        if ($pageData && is_array($pageData)) {
            $this->_data = $pageData + $this->_globalData;
        } else {
            $this->_data = $this->_globalData;
        }

        $this->_fillTemplateData($page);
        $this->_loadGlobalProcessor();
        $this->_loadGlobalConfiguration();

        /*
         * Process page contents, this loads the view from the pages controller.
         */
        $this->_parseSubPage();
        $this->_parsePage($page);


        $templateHtml = $this->_site->parser->parse($this->_getTemplatePath(false), $this->_data, TRUE);

        $this->_renderCommands($templateHtml);


        if ($return) {
            return $templateHtml;
        } else {
            echo $templateHtml;
            return true;
        }
    }

    private function _loadGlobalProcessor()
    {
        if (file_exists($this->_getPathInsideTheme() . 'processors/global.php')) {
            require $this->_getPathInsideTheme() . 'processors/global.php';
        }
    }

    private function _loadGlobalConfiguration()
    {
        if (file_exists($this->_getPathInsideTheme() . 'config.php')) {
            require $this->_getPathInsideTheme() . 'config.php';

            // Process the config array in config.php
            if (isset($config) && is_array($config)) {
                foreach ($config as $configKey => $configValue) {
                    if (!array_key_exists($configKey, $this->_data)) {
                        $this->_data[$configKey] = $configValue;
                    }
                }
            }

            // Process the autoload in config.php
            if (isset($autoload) && is_array($autoload)) {
                foreach ($autoload as $autloadKey => $autloadValue) {
                    $result = null;
                    if (!array_key_exists($autloadKey, $this->_data)) {
                        $result = $this->themeInclude($autloadValue);
                        if ($result) {
                            $this->_data[$autloadKey] = $result;
                        }
                    }
                }
            }
        }
    }

    private function _pathsData()
    {
        $this->_data['siteTitle'] = $this->_site->title;
        $this->_data['domainTitle'] = $this->_site->domainTitle;
        $this->_data['siteUrl'] = $this->_site->request->url->getFullHost();
        $this->_data['siteUrlBase'] = $this->_site->request->url->getBaseUrl();
        $this->_data['siteUrlStatic'] = $this->_data['siteUrl'];
        $this->_data['siteUrlStaticBase'] = $this->_data['siteUrlBase'];
        $this->_data['siteDomain'] = $this->_site->request->url->getDomain() . '.' . $this->_site->request->url->getDomainExtension();

        $this->_data['currentUrl'] = $this->_site->request->url->getUrl();
        $this->_data['baseUrl'] = $this->_site->request->url->getFullHost();

        $this->_data['pathResources'] = $this->_getPathInsideTheme() . 'resources/';

        $this->_data['pathCSS'] = $this->_data['siteUrlStatic'] . '/' . $this->_data['pathResources'] . 'css';
        $this->_data['pathImages'] = $this->_data['siteUrlStatic'] . '/' . $this->_data['pathResources'] . 'images';
        $this->_data['pathJS'] = $this->_data['siteUrlStatic'] . '/' . $this->_data['pathResources'] . 'js';
        $this->_data['pathAddons'] = $this->_data['siteUrlStatic'] . '/' . $this->_data['pathResources'] . 'addons';
        $this->_data['pathData'] = $this->_data['siteUrlStatic'] . '/' . $this->_data['pathResources'] . 'data';
    }

    private function _pathsJSVars()
    {
        $assets = " var pathAssets = '{$this->_data['pathResources']}'; ";
        $css = " var pathCSS = '{$this->_data['pathCSS']}'; ";
        $images = " var pathImages = '{$this->_data['pathImages']}'; ";
        $js = " var pathJS = '{$this->_data['pathJS']}'; ";
        $data = " var pathData = '{$this->_data['pathData']}'; ";
        $addons = " var pathAddons = '{$this->_data['pathAddons']}'; ";

        $this->_data['pathsJSVars'] = "<script type='text/javascript'>{$assets} {$css} {$images} {$js} {$data} {$addons}</script>";
    }

    private function _fillTemplateData()
    {
        $this->_pathsData();
        $this->_pathsJSVars();


        $this->_addDataIfMissing('pageTitle');
        $this->_addDataIfMissing('pageSubTitle');
        $this->_addDataIfMissing('metaTitle', $this->_data['siteTitle']);
        $this->_addDataIfMissing('metaDescription');
        $this->_addDataIfMissing('metaKeywords');
        $this->_addDataIfMissing('metaAuthor');
        $this->_addDataIfMissing('metaCopyrights');
    }

    /**
     * Check if the given key is not already available in the template data, set it using
     * the given default value.
     *
     * @param string $key
     * @param string $default
     */
    private function _addDataIfMissing($key, $default = "")
    {
        if (!array_key_exists($key, $this->_data)) {
            $this->_data[$key] = $default;
        }
    }

    private function _pageExists($page)
    {
        if (!file_exists($this->_getPathInsideTheme() . 'pages/' . $page . '.php')) {
            throw new Exception($this->_getPathInsideTheme(false) . 'pages/' . $page . '.php was not found.');
        }
    }

    private function _parsePage($page)
    {
        $this->_data['pageContent'] = $this->_site->parser->parse($this->_getPathInsideTheme(false) . 'pages/' . $page, $this->_data, TRUE);
    }

    private function _parseSubPage()
    {
        if (array_key_exists('subPage', $this->_data) && $this->_data['subPage'] != '') {

            $this->_data['subPageContent'] = $this->_site->parser->parse($this->_getPathInsideTheme(false) . 'pages/' . $this->_data['subPage'], $this->_data, TRUE);
        }
    }

    private function _rawParsePage($page)
    {
        $this->_data['pageContent'] = $this->_site->parser->parse($this->_getPathInsideTheme(false) . $page, $this->_data, TRUE);
    }

    /**
     * This function is normally used when the required view is divided in two parts,
     * normally such condtion is use ful when a page is part of both normal web request & ajax request.
     * In such case the part that loads by ajax, can be made a sub page & when its normal web request, this
     * page will be provided as subPage to the rendering call.
     *
     * @param string $this->_data
     */
    private function _rawParseSubPage()
    {
        if (array_key_exists('subPage', $this->_data) && $this->_data['subPage'] != '') {

            $this->_data['subPageContent'] = $this->_site->parser->parse($this->_getPathInsideTheme(false) . $this->_data['subPage'], $this->_data, TRUE);
        }
    }

    /**
     * Processes template commands in format {theme:command: @filename@ o:option='value' o:option1='value'}
     * In the above format "theme:" is the keyword to initiate command which follows with the command itself.
     * Notice the ":" period at the end of command in the format, this period is used as closer
     * Depending on the command it may ask for a filename and(or) options where filename/path can be provided
     * by putting it in "@" based colsure "@filename@" and options can be providing using keywork "o:" after which
     * option name & its value following like "o:option='value'". Any other elements in the command which are not
     * recognized will be ignored.
     *
     * Available Commands
     *
     * 1). include
     *      Requires: filename
     *      Optional: o:cahced="integer value"
     *
     * @param string $html
     */
    private function _renderCommands(&$html)
    {
        $commands = null;

        if ($html && $html != '') {
            preg_match_all('/(?P<fullCommand>\{theme\:(?P<command>[a-zA-Z]+?)\:\s?(?P<values>.*?)\})/', $html, $commands, PREG_SET_ORDER);
            if ($commands && count($commands) > 0) {
                foreach ($commands as $command) {
                    if (array_key_exists('fullCommand', $command) && array_key_exists('command', $command) && array_key_exists('values', $command)) {
                        $commandFull = trim($command['fullCommand']);
                        if (!array_key_exists($commandFull, $this->_commands)) {
                            $commandKey = trim($command['command']);
                            $commandValue = trim($command['values']);
                            if ($commandKey != '') {
                                switch ($commandKey) {
                                    case "include":
                                        $this->_processIncludeCommand($html, $commandFull, $commandKey, $commandValue);
                                        break;
                                }
                            }
                        } else {
                            $html = str_replace($commandFull, '' . $this->_commands[$commandFull], $html);
                        }
                    }
                }
            }
        }
    }

    /**
     *
     * @param string $html
     * @param string $commandFull
     * @param string $commandKey
     * @param string $commandValue
     */
    private function _processIncludeCommand(&$html, &$commandFull, &$commandKey, &$commandValue)
    {
        if (!array_key_exists($commandFull, $this->_commands)) {
            $commandResult = null;
            if ($commandValue != '') {
                $filename = $this->_extractCommandFileName($commandValue);
                $options = $this->_extractCommandOptions($commandValue); // TODO: process option for cache.
                if ($filename) {
                    $path = $this->_getPathInsideTheme(false) . "{$filename}";
                    $pathFull = $this->_getPathInsideTheme() . "{$filename}";
                    if (file_exists($pathFull . '.php')) {

                        $commandResult = $this->_site->parser->parse($path, $this->_data, TRUE);
                        $this->_renderCommands($commandResult);
                    }
                }
            }
            $this->_commands[$commandFull] = $commandResult;
        }

        $html = str_replace($commandFull, '' . $this->_commands[$commandFull], $html);
    }

    private function _extractCommandFileName(&$values)
    {
        if ($values) {
            $matches = null;
            preg_match('/\@([a-zA-Z0-9\.\-\/]+)\@/', $values, $matches);
            if ($matches && count($matches) > 0) {
                if (isset($matches[1])) {
                    if (trim($matches[1]) != '') {
                        return trim($matches[1]);
                    }
                }
            }
        }
        return null;
    }

    private function _extractCommandOptions(&$values)
    {
        return null;
    }

    public function themeInclude($file, $data = false)
    {
        $path = $this->_getPathInsideTheme(false) . "{$file}";
        $pathFull = $this->_getPathInsideTheme() . "{$file}";

        if (!array_key_exists($path, $this->_partials)) {
            if (file_exists($pathFull . '.php')) {
                if ($data && is_array($data)) {
                    $this->_data = $this->_data + $data;
                }

                $include = $this->_site->parser->parse($path, $this->_data, TRUE);
                $this->_renderCommands($include);
                $this->_partials[$file] = $include;
                return $this->_partials[$file];
            } else {
                throw new Exception("Theme Include: {$path}.php does not exist.");
            }
        } else {
            return $this->_partials[$file];
        }
    }

    public function themePartial($file, $data = false)
    {
        return $this->themeInclude('partials/' . $file, $data);
    }

    public function themePage($file, $data = false)
    {
        return $this->themeInclude('pages/' . $file, $data);
    }

    public function addData($key, $value)
    {
        if (!array_key_exists($key, $this->_data)) {
            $this->_data[$key] = $value;
        }
    }

    public function setData($key, $value)
    {
        $this->_data[$key] = $value;
    }

    public function getData($key, $default = '')
    {
        if (array_key_exists($key, $this->_data)) {
            return $this->_data[$key];
        } else {
            return $default;
        }
    }

    public function setGlobalData($key, $value)
    {
        $this->_globalData[$key] = $value;
    }

    public function getGlobalData($key, $default = null)
    {
        if (array_key_exists($key, $this->_globalData)) {
            return $this->_globalData[$key];
        } else {
            return $default;
        }
    }

    public function getUseMedia()
    {
        return $this->_useMedia;
    }

    public function setUseMedia($useMedia)
    {
        $this->_useMedia = $useMedia;
        return $this;
    }

    /**
     *
     * @return Site
     */
    public function getSite()
    {
        return $this->_site;
    }

    /**
     * Static function to make it easy to calle themeInclude from theme files.
     * Use this function from anywhere within the theme like Theme::tInclude($file)
     *
     * @param string $file
     * @return string
     */
    public static function tInclude($file, $data = false)
    {
        $ci = &get_instance();
        return $ci->site->theme->themeInclude($file, $data);
    }

    /**
     * Static function to make it easy to calle themeInclude from theme files.
     * Use this function from anywhere within the theme like Theme::tInclude($file)
     *
     * @param string $file
     * @return string
     */
    public static function tPartial($file, $data = false)
    {
        $ci = &get_instance();
        return $ci->site->theme->themePartial($file, $data);
    }

    /**
     * Static function to make it easy to calle themeInclude from theme files.
     * Use this function from anywhere within the theme like Theme::tInclude($file)
     *
     * @param string $file
     * @return string
     */
    public static function tPage($file, $data = false)
    {
        $ci = &get_instance();
        return $ci->site->theme->themePage($file, $data);
    }

    /**
     * Add new variable to current theme session.
     *
     * @param string $key
     * @param mixed $data
     */
    public static function tAddData($key, $data)
    {
        $ci = &get_instance();
        return $ci->site->theme->addData($key, $data);
    }

}
