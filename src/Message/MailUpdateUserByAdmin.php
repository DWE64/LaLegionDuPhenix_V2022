<?php


namespace App\Message;


class MailUpdateUserByAdmin
{
    private $email;
    private $name;
    private $firstname;
    private $urlContact;
    private $urlProfil;

    public function __construct($email, $name, $firstname, $urlContact, $urlProfil)
    {
        $this->email = $email;
        $this->name = $name;
        $this->firstname = $firstname;
        $this->urlContact = $urlContact;
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
    public function getUrlContact()
    {
        return $this->urlContact;
    }

    /**
     * @return mixed
     */
    public function getUrlProfil()
    {
        return $this->urlContact;
    }


}