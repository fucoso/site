<?php

namespace Fucoso\Site\Units;

use Exception;
use Fucoso\Site\Interfaces\IConfig;

/**
 *
 */
class Config
{

    /**
     *
     * @var array
     */
    protected $data = array();

    /**
     * Config file name used for loading site settings.
     * @var string
     */
    protected $group = 'site';

    public function __construct()
    {

    }

    public function getData()
    {
        return $this->data;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function setGroup($group)
    {
        $this->group = $group;
        return $this;
    }

    /**
     *
     * @param IConfig $provider
     * @return type
     */
    public function load(IConfig $provider)
    {
        if (!$this->group) {
            return;
        }

        $results = $provider->loadFromGroup($this->group);
        if (!$results) {
            return;
        }

        foreach ($results as $key => $value) {
            try {
                if (is_array($value)) {
                    $value = json_decode(json_encode($value)); // An easy way of converting all data to objects.
                }
                $this->data[$key] = $value;
            } catch (Exception $ex) {

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

    public function getAll()
    {
        return $this->data;
    }

}
