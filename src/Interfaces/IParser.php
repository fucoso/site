<?php

namespace Fucoso\Site\Interfaces;

interface IParser
{

    public function isReady();

    /**
     *
     * @param string $view
     * @param array $viewData
     * @param boolean $returnOutput
     */
    public function parse($view, $viewData, $returnOutput = false);

    /**
     *
     * @param string $string
     * @param array $viewData
     * @param boolean $returnOutput
     */
    public function parseString($string, $viewData, $returnOutput = false);

    /**
     *
     * @param string $file
     * @param array $viewData
     * @param boolean $returnOutput
     */
    public function parseFile($file, $viewData, $returnOutput = false);

    public function getThemesDirectory();

    public function getLayoutsVerb();

    public function getViewsVerb();

    public function getThemeViewDirectories();

    public function getFileExtension();

    public function output($content);
}
