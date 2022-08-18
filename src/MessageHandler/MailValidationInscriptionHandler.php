<?php


namespace App\MessageHandler;

use App\Message\MailRegistration;
use App\Message\MailValidationInscription;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class MailValidationInscriptionHandler extends AbstractMessageHandler
{
    public function __invoke(MailValidationInscription $mail)
    {
        try {
            $email=(new TemplatedEmail())
                ->from('no-reply@lalegionduphenix.com')
                ->to($mail->getEmail())
                ->subject($this->translator->trans('mail.subject.validation_inscription'))
                ->htmlTemplate('admin/mail/mail_validation_inscription.html.twig')
                ->context(
                    [
                        'firstname' => $mail->getFirstname(),
                        'name' => $mail->getName(),
                        'urlProfilTarget' => $mail->getUrlProfil()
                    ]
                );
            $this->mailer->send($email);
            return;
        }catch (TransportExceptionInterface $e){
            print_r($e);
            return null;
        }
    }
}