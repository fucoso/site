<?php

namespace Fucoso\Site\Units;

use Exception;
use Fucoso\Site\Interfaces\ISession;

class Bubble
{

    /**
     *
     * @var ISession
     */
    protected $sessionProvider;

    /**
     *
     * @var array
     */
    protected $nextData = array();

    /**
     *
     * @var array
     */
    protected $currentData = array();

    /**
     *
     * @var string
     */
    protected $sessionKey = 'siteBubbleData';

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
        if (count($this->nextData) > 0) {
            $data = serialize($this->nextData);
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
                    $this->currentData = $unserialized;
                }
            } catch (Exception $e) {

            }
        }
        $this->sessionProvider->remove($this->sessionKey);
    }

    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->currentData)) {
            return $this->currentData[$key];
        } else {
            return $default;
        }
    }

    public function set($key, $value)
    {
        $this->nextData[$key] = $value;
        return $this;
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function __set($key, $value)
    {
        $this->nextData[$key] = $value;
    }

    public function clear()
    {
        $this->nextData = array();
    }

    public function propagate($key)
    {
        if ($this->get($key, false)) {
            $this->set($key, $this->get($key));
        }
        return $this;
    }

    public function getAll()
    {
        return $this->currentData;
    }

}
