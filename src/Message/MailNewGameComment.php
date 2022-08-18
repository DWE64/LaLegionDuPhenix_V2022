<?php


namespace App\Message;


class MailNewGameComment
{
    private $email;
    private $name;
    private $firstname;
    private $urlProfil;
    private $gameTitle;

    public function __construct($email, $name, $firstname, $urlProfil, $gameTitle)
    {
        $this->email = $email;
        $this->name = $name;
        $this->firstname = $firstname;
        $this->urlProfil = $urlProfil;
        $this->gameTitle = $gameTitle;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @return mixed
     */
    public function getUrlProfil()
    {
        return $this->urlProfil;
    }

    /**
     * @return mixed
     */
    public function getGameTitle()
    {
        return $this->gameTitle;
    }

}