<?php

namespace Fucoso\Site\Units;

use Exception;
use Fucoso\Site\Site;

/**
 * 
 */
class Config
{

    /**
     *
     * @var Site 
     */
    private $_site = null;

    /**
     * Settings List.
     * @var array 
     */
    private $_properties = array();

    /**
     * Config file name used for loading site settings.
     * @var string 
     */
    private $_configFile = 'site';

    public function __construct(Site $site)
    {
        $this->_site = $site;
        $this->_load();
    }

    /**
     * Fetch all settings from database.
     */
    private function _load()
    {
        if ($this->_configFile) {
            //CodeIgniter Dependent.
            if ($this->_site->ci->config->load($this->_configFile, true, true)) {
                //CodeIgniter Dependent.
                $results = $this->_site->ci->config->item($this->_configFile);
                if ($results) {
                    foreach ($results as $key => $value) {
                        if (!array_key_exists($key, $this->_properties)) {
                            try {
                                if (is_array($value)) {
                                    $value = json_decode(json_encode($value)); // An easy way of converting all data to objects.
                                }
                                $this->_properties[$key] = $value;
                            } catch (Exception $ex) {
                                
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Get a single setting value this function is normally called by __get function.
     * 
     * @param string $name
     * @param string $default
     * @return string|null
     */
    private function _getSetting($name, $default = null)
    {
        if (array_key_exists($name, $this->_properties)) {
            if ($this->_properties[$name] === null) {
                return $default;
            } else {
                return $this->_properties[$name];
            }
        } else {
            return $default;
        }
    }

    /**
     * Magic function to fetch the retrieved settings are class attributes.
     * 
     * @param string $name
     * @param string $default
     * @return string|null
     */
    public function __get($name)
    {
        return $this->_getSetting($name, FALSE);
    }

    /**
     * Get all properties held in the config array.
     * @return Config[]
     */
    public function getAll()
    {
        return $this->_properties;
    }

}
