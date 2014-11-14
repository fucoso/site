<?php

namespace Fucoso\Site\Units;

use Exception;
use Fucoso\Site\Site;

class Notices
{

    const SUCCESS = 'success';
    const ERROR = 'error';
    const NOTIFICATION = 'notification';
    const WARNING = 'warning';
    const INFORMATION = 'information';

    /**
     *
     * @var Site 
     */
    private $_site = null;

    /**
     *
     * @var array 
     */
    private $_messages = array();

    /**
     *
     * @var type 
     */
    private $_oldMessages = array();

    /**
     *
     * @var type 
     */
    private $_sessionPrefix = 'site';

    /**
     * Initiate the Notices Object.
     * 
     * @param Site $site
     */
    public function __construct(Site $site)
    {
        $this->_site = $site;
        $this->_loadNotices();
    }

    public function __destruct()
    {
        $messages = serialize($this->_messages);
        $this->_setUserData($this->_sessionPrefix . 'notices', $messages);
    }

    private function _loadNotices()
    {
        if ($this->_site->session) {
            $messages = $this->_site->session->userdata($this->_sessionPrefix . 'notices');

            if ($messages && $messages != '') {
                try {
                    $messages_data = unserialize($messages);
                    if (is_array($messages_data)) {
                        $this->_oldMessages = $messages_data;
                    }
                } catch (Exception $e) {
                    
                }
            }
            $this->_site->session->unset_userdata($this->_sessionPrefix . 'notices');
        }
    }

    private function _setUserData($newdata = array(), $newval = '')
    {
        if ((php_sapi_name() == 'cli') or defined('STDIN')) {
            return;
        }

        if (is_string($newdata)) {
            $newdata = array($newdata => $newval);
        }

        if (count($newdata) > 0) {
            foreach ($newdata as $key => $val) {
                $_SESSION[$key] = $val;
            }
        }
    }

    /**
     * Push a notice to be accessible in the upcomming session.
     * 
     * @param string $key
     * @param string $title
     * @param string $message
     * @param string $class
     * @param boolean $hide
     * @param boolean $sticky
     * @return self
     */
    public function push($key, $title, $message, $class = self::INFORMATION, $hide = false, $sticky = false)
    {
        $this->_messages[$key] = array(
            'message' => $message,
            'class' => $class,
            'title' => $title,
            'hide' => $hide,
            'sticky' => $sticky,
        );
        return $this;
    }

    /**
     * Push a notice for current session.
     * 
     * @param string $key
     * @param string $title
     * @param string $message
     * @param string $class
     * @param boolean $hide
     * @param boolean $sticky
     * @return self
     */
    public function set($key, $title, $message, $class = self::INFORMATION, $hide = false, $sticky = false)
    {
        $this->_oldMessages[$key] = array(
            'message' => $message,
            'class' => $class,
            'title' => $title,
            'hide' => $hide,
            'sticky' => $sticky,
        );
        return $this;
    }

    /**
     * Get a single notice or all notices for current session by skiping the $key parameter.
     * 
     * @param string $key optional
     * @return array|null
     */
    public function get($key = FALSE)
    {
        if ($key) {
            if (array_key_exists($key, $this->_oldMessages)) {
                return $this->_oldMessages[$key];
            } else {
                return null;
            }
        } else {
            return $this->_oldMessages;
        }
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->_oldMessages)) {
            return $this->_oldMessages[$key];
        } else {
            return null;
        }
    }

    /**
     * Propogate the notice for the given key to the upcomming session.
     * 
     * @param string $key
     */
    public function propagate($key = false)
    {
        if ($key !== false) {
            if ($this->get($key)) {
                $this->_messages[$key] = $this->get($key);
            }
        } else {
            foreach ($this->_oldMessages as $messageKey => $message) {
                if (!array_key_exists($messageKey, $this->_messages)) {
                    $this->_messages[$messageKey] = $message;
                }
            }
        }
    }

    /**
     * Clean all messages for upcomming session.
     */
    public function clear()
    {
        $this->_messages = array();
    }

}
