<?php

namespace Fucoso\Site;

use CI_DB_active_record;
use Fucoso\Site\Interfaces\ParserInterface;
use Fucoso\Site\Interfaces\SessionInterface;
use Fucoso\Site\Request\Request;
use Fucoso\Site\Units\Auth;
use Fucoso\Site\Units\Cache;
use Fucoso\Site\Units\Config;
use Fucoso\Site\Units\Email;
use Fucoso\Site\Units\Hold;
use Fucoso\Site\Units\Next;
use Fucoso\Site\Units\Notices;
use Fucoso\Site\Units\Settings;
use MY_Controller;

/**
 *
 */
class Site
{

    /**
     *
     * @var Site
     */
    public static $sharedInstance = null;

    /**
     *
     * @var MY_Controller
     */
    public $ci = null;

    /**
     *
     * @var CI_DB_active_record
     */
    public $db = null;

    /**
     *
     * @var string
     */
    public $title = '';

    /**
     *
     * @var string
     */
    public $domainTitle = '';

    /**
     *
     * @var Request
     */
    public $request = null;

    /**
     *
     * @var Config
     */
    public $config = NULL;

    /**
     *
     * @var Settings
     */
    public $settings = NULL;

    /**
     *
     * @var Next
     */
    public $next = NULL;

    /**
     *
     * @var Hold
     */
    public $hold = NULL;

    /**
     *
     * @var Notices
     */
    public $notices = NULL;

    /**
     *
     * @var SessionInterface
     */
    public $session = null;

    /**
     *
     * @var Auth
     */
    public $auth = null;

    /**
     *
     * @var Cache
     */
    public $cache = null;

    /**
     *
     * @var Email
     */
    public $email = null;

    /**
     *
     * @var Theme
     */
    public $theme = null;

    /**
     *
     * @var ParserInterface
     */
    public $parser = null;

    public function __construct()
    {
        $this->_load();
    }

    private function _load()
    {
        $this->ci = &get_instance();

        //Load Auth Library.
        //CodeIgniter Dependent.
        $this->ci->load->library('session');
        $this->session = $this->ci->session;

        //Load Parser Library
        //CodeIgniter Dependent.
        $this->ci->load->library('parser');
        $this->parser = $this->ci->parser;
    }

    public function initialize()
    {
        //Load Site Request
        $this->request = new Request($this);
        $this->request->initialize();

        //Always make sure that Config is loaded before everything other than url.
        $this->config = new Config($this);

        if ($this->config->siteUseDatabase) {
            //Load the database object to be used by whole site.
            $this->db = $this->ci->load->database($this->config->siteDatabaseGroup, true);
        }

        $this->settings = new Settings($this);

        //Initiate Next Session Data Container.
        $this->next = new Next($this);

        //Initiate Persistent Session Data Container.
        $this->hold = new Hold($this);

        //Initiate Noticies Container.
        $this->notices = new Notices($this);

        //Load Site Cache
        $this->cache = new Cache($this);

        //Load Site Email Processor
        $this->email = new Email($this);

        //At the end initiate the theme object.
        $this->theme = new Theme($this);

        //Process site settings and apply changes if any needed.
        $this->_processConfiguration();

        //Load Auth Library.
        $this->auth = new Auth($this);
        $this->auth->loginUserFromSession();
    }

    private function _processConfiguration()
    {
        $this->title = '' . $this->settings->title;
        $this->domainTitle = '' . $this->settings->domainTitle;
    }

    public static function &getSharedInstance()
    {
        if (!self::$sharedInstance) {
            self::$sharedInstance = new Site();
            self::$sharedInstance->initialize();
        }

        return self::$sharedInstance;
    }

}
