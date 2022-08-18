<?php


namespace App\MessageHandler;


use App\Message\MailDeleteGame;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class MailDeleteGameHandler extends AbstractMessageHandler
{
    public function __invoke(MailDeleteGame $mail)
    {
        try {
            $email=(new TemplatedEmail())
                ->from('no-reply@lalegionduphenix.com')
                ->to($mail->getEmail())
                ->subject($this->translator->trans('mail.subject.delete_game'))
                ->htmlTemplate('admin/mail/mail_delete_game.html.twig')
                ->context(
                    [
                        'firstname' => $mail->getFirstname(),
                        'name' => $mail->getName(),
                        'urlContactTarget' => $mail->getUrlContact(),
                        'title' => $mail->getGameTitle()
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