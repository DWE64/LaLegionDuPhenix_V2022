<?php


namespace App\Message;


class MailContact
{
    private $email;
    private $name;
    private $firstname;
    private $phone;
    private $subject;
    private $content;

    public function __construct($email, $name, $firstname, $phone, $subject, $content)
    {
        $this->email = $email;
        $this->name = $name;
        $this->firstname = $firstname;
        $this->phone = $phone;
        $this->subject = $subject;
        $this->content = $content;
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
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }
}