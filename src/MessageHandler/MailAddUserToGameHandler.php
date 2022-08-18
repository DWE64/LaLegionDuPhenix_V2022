<?php


namespace App\MessageHandler;


use App\Message\MailAddUserToGame;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class MailAddUserToGameHandler extends AbstractMessageHandler
{
    public function __invoke(MailAddUserToGame $mail)
    {
        try {
            $email=(new TemplatedEmail())
                ->from('no-reply@lalegionduphenix.com')
                ->to($mail->getEmail())
                ->subject($this->translator->trans('mail.subject.add_user_to_game'))
                ->htmlTemplate('admin/mail/mail_add_userToGame.html.twig')
                ->context(
                    [
                        'firstname' => $mail->getFirstname(),
                        'name' => $mail->getName(),
                        'urlProfilTarget' => $mail->getUrlProfil(),
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