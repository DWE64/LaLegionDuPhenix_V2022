<?php


namespace App\MessageHandler;

use App\Message\MailRegistration;
use App\Message\MailRemoveInscription;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class MailRemoveInscriptionHandler extends AbstractMessageHandler
{
    public function __invoke(MailRemoveInscription $mail)
    {
        try {
            $email=(new TemplatedEmail())
                ->from('no-reply@lalegionduphenix.com')
                ->to($mail->getEmail())
                ->subject($this->translator->trans('mail.subject.remove_inscription'))
                ->htmlTemplate('admin/mail/mail_remove_inscription.html.twig')
                ->context(
                    [
                        'firstname' => $mail->getFirstname(),
                        'name' => $mail->getName(),
                        'urlContactTarget' => $mail->getUrlContact()
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