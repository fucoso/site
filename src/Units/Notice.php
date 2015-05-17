<?php

namespace Fucoso\Site\Units;

class Notice
{

    const SUCCESS = 'success';
    const ERROR = 'error';
    const NOTIFICATION = 'notification';
    const WARNING = 'warning';
    const INFORMATION = 'information';

    /**
     *
     * @var string
     */
    protected $title;

    /**
     *
     * @var string
     */
    protected $message;

    /**
     *
     * @var string
     */
    protected $statusClass = self::INFORMATION;

    /**
     *
     * @var boolean
     */
    protected $hideAfterInternal = false;

    /**
     *
     * @var boolean
     */
    protected $remainSticked = false;

    public function __construct()
    {

    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getStatusClass()
    {
        return $this->statusClass;
    }

    public function getHideAfterInternal()
    {
        return $this->hideAfterInternal;
    }

    public function getRemainSticked()
    {
        return $this->remainSticked;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    public function setStatusClass($statusClass)
    {
        $this->statusClass = $statusClass;
        return $this;
    }

    public function setHideAfterInternal($hideAfterInternal)
    {
        $this->hideAfterInternal = $hideAfterInternal;
        return $this;
    }

    public function setRemainSticked($remainSticked)
    {
        $this->remainSticked = $remainSticked;
        return $this;
    }

    public function isInformation()
    {
        if ($this->getStatusClass() == self::INFORMATION) {
            return true;
        }
        return false;
    }

    public function isSuccess()
    {
        if ($this->getStatusClass() == self::SUCCESS) {
            return true;
        }
        return false;
    }

    public function isError()
    {
        if ($this->getStatusClass() == self::ERROR) {
            return true;
        }
        return false;
    }

    public function isNotification()
    {
        if ($this->getStatusClass() == self::NOTIFICATION) {
            return true;
        }
        return false;
    }

    public function isWarning()
    {
        if ($this->getStatusClass() == self::WARNING) {
            return true;
        }
        return false;
    }

    public static function create($title, $message)
    {
        $notice = new static();
        $notice->setTitle($title);
        $notice->setMessage($message);
        return $notice;
    }

    public static function information($title, $message)
    {
        return static::create($title, $message);
    }

    public static function success($title, $message)
    {
        $notice = static::create($title, $message);
        $notice->setStatusClass(self::SUCCESS);
        return $notice;
    }

    public static function error($title, $message)
    {
        $notice = static::create($title, $message);
        $notice->setStatusClass(self::ERROR);
        return $notice;
    }

    public static function notification($title, $message)
    {
        $notice = static::create($title, $message);
        $notice->setStatusClass(self::NOTIFICATION);
        return $notice;
    }

    public static function warning($title, $message)
    {
        $notice = static::create($title, $message);
        $notice->setStatusClass(self::WARNING);
        return $notice;
    }

}
