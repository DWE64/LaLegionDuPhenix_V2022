<?php


namespace App\Message;


class MailDeleteUserToGame
{
    private $email;
    private $name;
    private $firstname;
    private $urlContact;
    private $gameTitle;

    public function __construct($email, $name, $firstname, $urlContact, $gameTitle)
    {
        $this->email = $email;
        $this->name = $name;
        $this->firstname = $firstname;
        $this->urlContact = $urlContact;
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
    public function getUrlContact()
    {
        return $this->urlContact;
    }

    /**
     * @return mixed
     */
    public function getGameTitle()
    {
        return $this->gameTitle;
    }

}