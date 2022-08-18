<?php


namespace App\MessageHandler;

use App\Message\MailAddNewGame;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class MailAddNewGameHandler extends AbstractMessageHandler
{
    public function __invoke(MailAddNewGame $mail)
    {
        try {
            $email=(new TemplatedEmail())
                ->from('no-reply@lalegionduphenix.com')
                ->to($mail->getEmail())
                ->subject($this->translator->trans('mail.subject.add_new_game'))
                ->htmlTemplate('admin/mail/mail_new_game.html.twig')
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