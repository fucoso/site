<?php

namespace Fucoso\Site\Interfaces;

interface ITemplate
{

    /**
     *
     * @param string $themeName
     */
    public function setThemeName($themeName);

    /**
     *
     * @param string $layoutView
     */
    public function setLayoutView($layoutView);

    /**
     *
     * @param string $verb
     */
    public function setVerb($verb);

    /**
     *
     * @param string $view
     * @param array $data
     */
    public function viewRaw($view, array $data = array());

    /**
     *
     * @param string $view
     * @param array $data
     * @param boolean $sendOutput
     */
    public function view($view, array $data = array(), $sendOutput = true);

    /**
     *
     * @param string $key
     * @param string $uri
     */
    public function addBreadcrumb($key, $uri);

    public function __get($key);

    public function __set($key, $value);
}
