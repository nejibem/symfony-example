<?php

namespace AppBundle\Service;

use Mailgun\Mailgun;

class Mail
{
    private $apiKey;
    private $mailgun;
    private $domain;

    public function __construct($apiKey, $domain)
    {
        $this->apiKey = $apiKey;
        $this->domain = $domain;
        $this->mailgun = new Mailgun($apiKey);
    }

    public function send($to, $from, $subject, $text)
    {
        $this->mailgun->sendMessage($this->domain, [
            'from'    => $to,
            'to'      => $from,
            'subject' => $subject,
            'text'    => $text,
        ]);
    }


}