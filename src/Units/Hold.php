<?php

namespace Fucoso\Site\Units;

use Exception;
use Fucoso\Site\Interfaces\ISession;

class Hold
{

    /**
     *
     * @var ISession
     */
    protected $sessionProvider;

    /**
     *
     * @var string
     */
    protected $data = array();

    /**
     *
     * @var string
     */
    protected $sessionKey = 'siteHoldData';

    /**
     *
     * @param ISession $sessionProvider
     */
    public function __construct(ISession $sessionProvider)
    {
        $this->sessionProvider = $sessionProvider;
    }

    public function __destruct()
    {
        if (count($this->data) > 0) {
            $data = serialize($this->data);
            $this->sessionProvider->save($this->sessionKey, $data);
        }
    }

    public function load()
    {
        $data = $this->sessionProvider->read($this->sessionKey);

        if ($data) {
            try {
                $unserialized = unserialize($data);
                if (is_array($unserialized)) {
                    $this->data = $unserialized;
                }
            } catch (Exception $e) {

            }
        }
        $this->sessionProvider->remove($this->sessionKey);
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        } else {
            return $default;
        }
    }

    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function reset($key)
    {
        if (array_key_exists($key, $this->data)) {
            unset($this->data[$key]);
        }
    }

    public function clear()
    {
        $this->data = array();
    }

    public function getAll()
    {
        return $this->data;
    }

}
