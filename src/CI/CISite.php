<?php

namespace Fucoso\Site\CI;

use Fucoso\Site\Site;
use Fucoso\Site\Units\Auth;
use Fucoso\Site\Units\Browser;
use Fucoso\Site\Units\Bubble;
use Fucoso\Site\Units\Cache;
use Fucoso\Site\Units\Config;
use Fucoso\Site\Units\Email;
use Fucoso\Site\Units\Hold;
use Fucoso\Site\Units\Notices;
use Fucoso\Site\Units\Settings;
use Fucoso\Site\Units\Template\Template;
use Fucoso\Site\Units\Url;

class CISite extends Site
{

    public static function &getSharedInstance()
    {
        if (!static::$sharedInstance) {
            $site = new CISite();

            $site->loadRequest();
            $site->loadConfig();
            $site->loadDatabase();
            $site->loadSettings();
            $site->loadSession();
            $site->loadCache();
            $site->loadEmail();
            $site->loadTemplate();
            $site->loadAuth();

            static::$sharedInstance = $site;
        }

        return static::$sharedInstance;
    }

    protected function loadRequest()
    {
        $this->url = new Url();
        $this->url->parse();

        $this->browser = new Browser();
        $this->browser->parse();
    }

    protected function loadConfig()
    {
        $this->config = new Config();
        $this->config->load(new CIConfig());
    }

    protected function loadDatabase()
    {
        $this->data = CIData::getSharedInstance();
        if ($this->config->siteUseDatabase) {
            $databaseGroup = $this->config->siteDatabaseGroup;
            if ($databaseGroup) {
                $this->data->load($databaseGroup);
            } else {
                $this->data->load();
            }
        }
    }

    protected function loadSettings()
    {
        $this->settings = new Settings();
        $this->settings->load($this->config, $this->data);
    }

    protected function loadSession()
    {
        $this->session = new CISession();

        $this->hold = new Hold($this->session);
        $this->hold->load();

        $this->bubble = new Bubble($this->session);
        $this->bubble->load();

        $this->notices = new Notices($this->session);
        $this->notices->load();
    }

    protected function loadCache()
    {
        $this->cache = new Cache(new CICache());
    }

    protected function loadEmail()
    {
        $this->email = new Email(new CIEmail());

        $this->email->setFromDomain($this->url->getQualifiedDomain());
        $this->email->setFromTitle('' . $this->settings->siteTitle);

        $parser = new CIParser();
        $parser->load();

        $this->email->setTemplate(new Template($parser));
        $this->email->getTemplate()->setVerb('emails');
    }

    protected function loadTemplate()
    {
        $parser = new CIParser();
        $parser->load();

        $this->template = new Template($parser);
    }

    protected function loadAuth()
    {
        $this->auth = new Auth($this->session, $this->data);
        $this->auth->load($this->config);
    }

}
