<?php

namespace App\Tests;

use App\Entity\Game;
use App\Entity\StatusUserInGame;
use App\Entity\User;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp():void
    {
        parent::setUp();
        $this->user = new User();
    }

    public function testGetEmail(): void
    {
        $value = 'test@test.fr';
        $response = $this->user->setEmail($value);

        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($value, $this->user->getEmail());

    }

    public function testGetRoles():void
    {
        $value =['ROLE_ADMIN'];
        $response = $this->user->setRoles($value);

        $this->assertInstanceOf(User::class, $response);
        $this->assertContains('ROLE_USER', $this->user->getRoles());
        $this->assertContains('ROLE_ADMIN', $this->user->getRoles());
    }

    public function testGetPassword():void
    {
        $value ='password';
        $response = $this->user->setPassword($value);

        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($value, $this->user->getPassword());
    }
    public function testGetName():void
    {
        $value ='Doe';
        $response = $this->user->setName($value);

        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($value, $this->user->getName());
    }
    public function testGetFirstname():void
    {
        $value ='john';
        $response = $this->user->setFirstname($value);

        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($value, $this->user->getFirstname());
    }
    public function testGetBirthday():void
    {
        $value = new DateTime(date('d-m-Y H:i:s'));
        $response = $this->user->setBirthday($value);

        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($value, $this->user->getBirthday());
    }
    public function testGetPostalCode():void
    {
        $value ='78100';
        $response = $this->user->setPostalCode($value);

        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($value, $this->user->getPostalCode());
    }
    public function testGetCity():void
    {
        $value ='Paris';
        $response = $this->user->setCity($value);

        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($value, $this->user->getCity());
    }
    public function testGetAdress():void
    {
        $value ='7 chemin de loung';
        $response = $this->user->setAdress($value);

        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($value, $this->user->getAdress());
    }
    public function testGetMemberStatus():void
    {
        $value ='ACTIF';
        $response = $this->user->setMemberStatus($value);

        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($value, $this->user->getMemberStatus());
    }
    public function testGetMemberSeniority():void
    {
        $value ='new/old';
        $response = $this->user->setMemberSeniority($value);

        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($value, $this->user->getMemberSeniority());
    }
    public function testGetAssociationRegistrationDate():void
    {
        $value = new DateTime(date('d-m-Y H:i:s'));
        $response = $this->user->setAssociationRegistrationDate($value);

        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($value, $this->user->getAssociationRegistrationDate());
    }
    public function testGetCreatedAt():void
    {
        $value = new DateTimeImmutable(date('d-m-Y H:i:s'));
        $response = $this->user->setCreatedAt($value);

        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($value, $this->user->getCreatedAt());
    }
    public function testGetUpdatedAt():void
    {
        $value = new DateTimeImmutable(date('d-m-Y H:i:s'));
        $response = $this->user->setUpdatedAt($value);

        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($value, $this->user->getUpdatedAt());
    }
    public function testGetIsAssociationMember():void
    {
        $value = true;
        $response = $this->user->setIsAssociationMember($value);

        $this->assertInstanceOf(User::class, $response);
        $this->assertIsBool($this->user->isIsAssociationMember());
    }
    public function testGetGames():void
    {
        $value = new Game();
        $response = $this->user->addGame($value);

        $this->assertInstanceOf(User::class, $response);
        $this->assertCount(1, $this->user->getGames());
        $this->assertTrue($this->user->getGames()->contains($value));

        $response = $this->user->removeGame($value);

        $this->assertInstanceOf(User::class, $response);
        $this->assertCount(0, $this->user->getGames());
        $this->assertFalse($this->user->getGames()->contains($value));
    }
    public function testGetPlayerGame():void
    {
        $value = new Game();
        $response = $this->user->addPlayersGame($value);

        $this->assertInstanceOf(User::class, $response);
        $this->assertCount(1, $this->user->getPlayersGames());
        $this->assertTrue($this->user->getPlayersGames()->contains($value));

        $response = $this->user->removePlayersGame($value);

        $this->assertInstanceOf(User::class, $response);
        $this->assertCount(0, $this->user->getPlayersGames());
        $this->assertFalse($this->user->getPlayersGames()->contains($value));
    }
    public function testGetStatusUserInGame():void
    {
        $value = new StatusUserInGame();
        $response = $this->user->addStatusUserInGame($value);

        $this->assertInstanceOf(User::class, $response);
        $this->assertCount(1, $this->user->getStatusUserInGames());
        $this->assertTrue($this->user->getStatusUserInGames()->contains($value));

        $response = $this->user->removeStatusUserInGame($value);

        $this->assertInstanceOf(User::class, $response);
        $this->assertCount(0, $this->user->getStatusUserInGames());
        $this->assertFalse($this->user->getStatusUserInGames()->contains($value));
    }
}
