<?php

namespace Fucoso\Site\CI;

use CI_Email;
use Fucoso\Site\Interfaces\IEmail;

class CIEmail implements IEmail
{

    /**
     *
     * @var CI_Email
     */
    private $ciEmail;

    public function __construct()
    {
        $ci = get_instance();
        if (isset($ci->ciEmail) && $ci->ciEmail) {
            $this->ciEmail = $ci->ciEmail;
        }
    }

    public function isReady()
    {
        if ($this->ciEmail) {
            return true;
        }
        return false;
    }

    public function bcc($bcc)
    {
        $this->ciEmail->bcc($bcc);
    }

    public function cc($cc)
    {
        $this->ciEmail->cc($cc);
    }

    public function clear()
    {
        $this->ciEmail->clear();
    }

    public function from($from, $fromName)
    {
        $this->ciEmail->from($from, $fromName);
    }

    public function message($message)
    {
        $this->ciEmail->message($message);
    }

    public function send()
    {
        return $this->ciEmail->send();
    }

    public function setMailType($type)
    {
        $this->ciEmail->mailtype = $type;
    }

    public function subject($subject)
    {
        $this->ciEmail->subject($subject);
    }

    public function to($to)
    {
        $this->ciEmail->to($to);
    }

}
