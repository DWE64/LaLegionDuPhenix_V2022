<?php


namespace App\Message;


class MailRemoveInscription
{
    private $email;
    private $name;
    private $firstname;
    private $urlContact;

    public function __construct($email, $name, $firstname, $urlContact)
    {
        $this->email = $email;
        $this->name = $name;
        $this->firstname = $firstname;
        $this->urlContact = $urlContact;
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


}