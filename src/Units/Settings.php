<?php

namespace Fucoso\Site\Units;

use Fucoso\Site\Site;

/**
 * 
 */
class Settings
{

    /**
     *
     * @var Site 
     */
    private $_site = null;

    /**
     * DB Table that holds the settings for this site.
     * @var string 
     */
    private $_table = 'settings';

    /**
     * DB Table column that holds the setting slug.
     * @var string 
     */
    private $_keyColumn = 'key';

    /**
     * DB Table column that holds the setting value.
     * @var string 
     */
    private $_valueColumn = 'value';

    /**
     * Settings List.
     * @var array 
     */
    private $_properties = array();

    public function __construct(Site $site)
    {
        $this->_site = $site;

        $this->_loadFromDatabase();
        $this->_loadFromConfig();
    }

    /**
     * Fetch all settings from database.
     */
    private function _loadFromDatabase()
    {
        if ($this->_site->db && $this->_site->config->settingsUseDatabase) {

            $this->_table = $this->_site->config->settingsTable;
            $this->_keyColumn = $this->_site->config->settingsKeyColumn;
            $this->_valueColumn = $this->_site->config->settingsValueColumn;

            if ($this->_table && $this->_keyColumn && $this->_valueColumn) {

                $query = "Select `{$this->_keyColumn}` as `key`, `{$this->_valueColumn}` as `value` From {$this->_table} ";
                $queryResult = $this->_site->db->query($query);

                if ($queryResult && $queryResult->num_rows() > 0) {
                    $results = $queryResult->result();
                    if ($results) {
                        foreach ($results as $row) {
                            $key = $row->key;
                            if (!array_key_exists($key, $this->_properties)) {
                                $this->_properties[$key] = $row->value;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Fetch all settings from config, this takes second priority & if 
     * a property has already been found in db, it will be ignored from config.
     */
    private function _loadFromConfig()
    {
        $results = $this->_site->config->getAll();
        if ($results) {
            foreach ($results as $key => $value) {
                if (!array_key_exists($key, $this->_properties)) {
                    $this->_properties[$key] = $value;
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

}
