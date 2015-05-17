<?php

namespace Fucoso\Site;

use Fucoso\Site\Interfaces\IData\IData;
use Fucoso\Site\Interfaces\ISession;
use Fucoso\Site\Interfaces\ISite;
use Fucoso\Site\Interfaces\ITemplate;
use Fucoso\Site\Units\Auth;
use Fucoso\Site\Units\Browser;
use Fucoso\Site\Units\Bubble;
use Fucoso\Site\Units\Cache;
use Fucoso\Site\Units\Config;
use Fucoso\Site\Units\Email;
use Fucoso\Site\Units\Hold;
use Fucoso\Site\Units\Notices;
use Fucoso\Site\Units\Settings;
use Fucoso\Site\Units\Url;

abstract class Site implements ISite
{

    /**
     *
     * @var Site
     */
    public static $sharedInstance = null;

    /**
     *
     * @var Url
     */
    public $url;

    /**
     *
     * @var Browser
     */
    public $browser;

    /**
     *
     * @var Config
     */
    public $config;

    /**
     *
     * @var IData
     */
    public $data;

    /**
     *
     * @var Settings
     */
    public $settings;

    /**
     *
     * @var ISession
     */
    public $session;

    /**
     *
     * @var Hold
     */
    public $hold;

    /**
     *
     * @var Bubble
     */
    public $bubble;

    /**
     *
     * @var Notices
     */
    public $notices;

    /**
     *
     * @var Auth
     */
    public $auth;

    /**
     *
     * @var Cache
     */
    public $cache;

    /**
     *
     * @var Email
     */
    public $email;

    /**
     *
     * @var ITemplate
     */
    public $template;

    public function getUrl()
    {
        return $this->url;
    }

    public function getBrowser()
    {
        return $this->browser;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getSettings()
    {
        return $this->settings;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getHold()
    {
        return $this->hold;
    }

    public function getBubble()
    {
        return $this->bubble;
    }

    public function getNotices()
    {
        return $this->notices;
    }

    public function getAuth()
    {
        return $this->auth;
    }

    public function getCache()
    {
        return $this->cache;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getTemplate()
    {
        return $this->template;
    }

}
