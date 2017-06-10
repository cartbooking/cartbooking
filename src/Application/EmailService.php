<?php

namespace CartBooking\Application;

use CartBooking\Model\Publisher\Publisher;
use Swift_Mailer;
use Swift_Message;

class EmailService
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;
    /**
     * @var Swift_Message
     */
    private $message;

    public function __construct(Swift_Mailer $mailer, Swift_Message $message)
    {
        $this->mailer = $mailer;
        $this->message = $message;
    }

    public function sendEmailTo(Publisher $recipient, string $subject, string $message)
    {
        $this->message->addTo($recipient->getEmail(), $recipient->getFirstName());
        $this->message->setSubject($subject);
        $this->message->setBody($message);
        try {
            $this->mailer->send($this->message);
        } catch (\Swift_TransportException $e) {
            ServiceLocator::getLogger()->addError($e->getMessage(), $e->getTrace());
        }
    }
}
