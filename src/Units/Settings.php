<?php

namespace Fucoso\Site\Units;

use Fucoso\Site\Interfaces\IData\IData;

class Settings
{

    /**
     * DB Table that holds the settings for this site.
     * @var string
     */
    protected $table = 'settings';

    /**
     * DB Table column that holds the setting slug.
     * @var string
     */
    protected $columnKey = 'key';

    /**
     * DB Table column that holds the setting value.
     * @var string
     */
    protected $columnValue = 'value';

    /**
     * Settings List.
     * @var array
     */
    protected $data = array();

    public function __construct()
    {

    }

    /**
     *
     * @param Config $config
     * @param IData $dbData
     */
    public function load(Config $config, IData $dbData)
    {
        $this->loadFromConfig($config);
        $this->loadFromDatabase($config, $dbData);
    }

    /**
     *
     * @param Config $config
     */
    protected function loadFromConfig(Config $config)
    {
        $results = $config->getAll();
        if ($results) {
            foreach ($results as $key => $value) {
                $this->data[$key] = $value;
            }
        }
    }

    /**
     *
     * @param Config $config
     * @param IData $dbData
     */
    protected function loadFromDatabase(Config $config, IData $dbData)
    {
        if ($dbData && $config->settingsUseDatabase) {

            $this->table = $config->settingsTable;
            $this->columnKey = $config->settingsKeyColumn;
            $this->columnValue = $config->settingsValueColumn;

            if ($this->table && $this->columnKey && $this->columnValue) {

                $sql = "Select `{$this->columnKey}` as `key`, `{$this->columnValue}` as `value` From {$this->table} ";
                $query = $dbData->query($sql);
                $results = $dbData->result($query);

                if ($results) {
                    foreach ($results as $row) {
                        $key = $row->key;
                        $this->data[$key] = $row->value;
                    }
                }
            }
        }
    }

    /**
     * Get a single setting value this function is normally called by __get function.
     *
     * @param string $key
     * @param string $default
     * @return string|null
     */
    protected function getSetting($key, $default = null)
    {
        if (array_key_exists($key, $this->data)) {
            if ($this->data[$key] === null) {
                return $default;
            } else {
                return $this->data[$key];
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
        return $this->getSetting($name, FALSE);
    }

}
