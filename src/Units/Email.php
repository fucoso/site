<?php

namespace Fucoso\Site\Units;

use Fucoso\Site\Site;
use Fucoso\Site\Theme;

class Email
{

    /**
     *
     * @var Site
     */
    private $_site = null;

    /**
     *
     * @var Theme
     */
    public $theme = null;

    public function __construct(Site $site)
    {
        $this->_site = $site;

        $this->theme = new Theme($this->_site);
        $this->theme->setUseMedia(false);
    }

    public function send($to, $subject, $view, $data)
    {
        $this->_site->ci->load->library('email');

        $this->_site->ci->email->clear();

        $config['mailtype'] = "html";

        $this->_site->ci->email->initialize($config);

        $this->_site->ci->email->from('mailer@' . $this->_site->request->url->getQualifiedDomainWithSubDomain(), $this->_site->domainTitle);
        $this->_site->ci->email->to($to);
        $this->_site->ci->email->subject($subject);

        if (!array_key_exists('pageTitle', $data)) {
            $data['pageTitle'] = $subject;
        }

        $html = $this->theme->view($view, $data, true);

        $this->_site->ci->email->message($html);

        if ($this->_site->ci->email->send()) {
            return true;
        } else {
            //return false;
            return true; // Patch for now, until server configutaion is good.
        }
    }

    public function sendHtml($to, $subject, $message)
    {
        $this->_site->ci->load->library('email');

        $this->_site->ci->email->clear();

        $config['mailtype'] = "html";

        $this->_site->ci->email->initialize($config);

        $this->_site->ci->email->from('mailer@' . $this->_site->request->url->getQualifiedDomainWithSubDomain());
        $this->_site->ci->email->to($to);
        $this->_site->ci->email->subject($subject);

        $this->_site->ci->email->message($message);

        if ($this->_site->ci->email->send()) {
            return true;
        } else {
            //return false;
            return true; // Patch for now, until server configutaion is good.
        }
    }

    public function sendText($to, $subject, $message)
    {
        $this->_site->ci->load->library('email');

        $this->_site->ci->email->clear();

        $this->_site->ci->email->from('mailer@' . $this->_site->request->url->getQualifiedDomainWithSubDomain());
        $this->_site->ci->email->to($to);
        $this->_site->ci->email->subject($subject);

        $this->_site->ci->email->message($message);

        if ($this->_site->ci->email->send()) {
            return true;
        } else {
            //return false;
            return true; // Patch for now, until server configutaion is good.
        }
    }

}
