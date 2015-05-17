<?php

namespace Fucoso\Site\Interfaces;

interface IEmail
{
    public function isReady();

    public function clear();

    public function setMailType($type);

    public function to($to);

    public function from($from, $fromName);

    public function cc($cc);

    public function bcc($bcc);

    public function subject($subject);

    public function message($message);

    public function send();
}
