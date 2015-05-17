<?php

namespace Fucoso\Site\Units;

use Exception;
use Fucoso\Site\Interfaces\ISession;

class Notices
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
     * @var type
     */
    protected $currentData = array();

    /**
     *
     * @var type
     */
    protected $sessionKey = 'siteNoticesData';

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

    /**
     * Push a notice to be accessible in the upcomming session.
     *
     * @param string $key
     * @param Notice $notice
     * @return self
     */
    public function push($key, Notice $notice)
    {
        $this->nextData[$key] = $notice;
        return $this;
    }

    /**
     * Get a single notice or all notices for current session by skiping the $key parameter.
     *
     * @param string $key
     * @return Notice
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->currentData)) {
            return $this->currentData[$key];
        } else {
            return null;
        }
    }

    /**
     * Push a notice for current session.
     *
     * @param string $key
     * @param Notice $notice
     * @return self
     */
    public function set($key, Notice $notice)
    {
        $this->currentData[$key] = $notice;
        return $this;
    }

    /**
     *
     * @param string $key
     * @return Notice
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    public function clear()
    {
        $this->nextData = array();
    }

    /**
     * Propogate the notice for the given key to the upcomming session.
     *
     * @param string $key
     */
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
