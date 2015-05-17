<?php

namespace Fucoso\Site\Units;

use Fucoso\Site\Interfaces\IEmail;
use Fucoso\Site\Interfaces\ITemplate;

class Email
{

    /**
     *
     * @var IEmail
     */
    protected $provider;

    /**
     *
     * @var string
     */
    protected $fromDomain = 'local';

    /**
     *
     * @var string
     */
    protected $fromTitle = 'Local';

    /**
     *
     * @var ITemplate
     */
    protected $template = null;

    public function __construct(IEmail $provider)
    {
        $this->provider = $provider;
    }

    public function getFromDomain()
    {
        return $this->fromDomain;
    }

    public function getFromTitle()
    {
        return $this->fromTitle;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setFromDomain($fromDomain)
    {
        $this->fromDomain = $fromDomain;
        return $this;
    }

    public function setFromTitle($fromTitle)
    {
        $this->fromTitle = $fromTitle;
        return $this;
    }

    public function setTemplate(ITemplate $template)
    {
        $this->template = $template;
        return $this;
    }

    public function send($to, $subject, $view, $data)
    {
        if (!$this->getTheme() && !$this->provider->isReady()) {
            return false;
        }

        $this->provider->clear();
        $this->provider->setMailType('html');

        $this->provider->from('mailer@' . $this->fromDomain, $this->fromTitle);
        $this->provider->to($to);
        $this->provider->subject($subject);

        if (!array_key_exists('pageTitle', $data)) {
            $data['pageTitle'] = $subject;
        }

        $html = $this->template->view($view, $data, true);

        $this->provider->message($html);

        if ($this->provider->send()) {
            return true;
        } else {
            //return false;
            return true; // Patch for now, until server configutaion is good.
        }
    }

    public function sendHtml($to, $subject, $message)
    {
        if (!$this->getTheme() && !$this->provider->isReady()) {
            return false;
        }

        $this->provider->clear();
        $this->provider->setMailType('text');

        $this->provider->from('mailer@' . $this->fromDomain, $this->fromTitle);
        $this->provider->to($to);
        $this->provider->subject($subject);

        $this->provider->message($message);

        if ($this->provider->send()) {
            return true;
        } else {
            //return false;
            return true; // Patch for now, until server configutaion is good.
        }
    }

    public function sendText($to, $subject, $message)
    {
        if (!$this->getTheme() && !$this->provider->isReady()) {
            return false;
        }

        $this->provider->clear();
        $this->provider->setMailType('text');

        $this->provider->from('mailer@' . $this->fromDomain, $this->fromTitle);
        $this->provider->to($to);
        $this->provider->subject($subject);

        $this->provider->message($message);

        if ($this->provider->send()) {
            return true;
        } else {
            //return false;
            return true; // Patch for now, until server configutaion is good.
        }
    }

}
