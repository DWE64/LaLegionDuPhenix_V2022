<?php


namespace App\MessageHandler;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


class AbstractMessageHandler
{
    protected MailerInterface $mailer;
    protected TranslatorInterface $translator;

    public function __construct(MailerInterface $mailer, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
    }
}