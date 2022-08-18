<?php


namespace App\Message;


class MailValidationInscription
{
    private $email;
    private $name;
    private $firstname;
    private $urlProfil;

    public function __construct($email, $name, $firstname, $urlProfil)
    {
        $this->email = $email;
        $this->name = $name;
        $this->firstname = $firstname;
        $this->urlProfil = $urlProfil;
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


}