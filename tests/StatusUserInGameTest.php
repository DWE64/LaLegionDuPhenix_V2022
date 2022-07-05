<?php

namespace App\Tests;

use App\Entity\Game;
use App\Entity\StatusUserInGame;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class StatusUserInGameTest extends TestCase
{
    private StatusUserInGame $statusUserInGame;

    protected function setUp(): void
    {
        parent::setUp();
        $this->statusUserInGame = new StatusUserInGame();
    }

    public function testUser(): void
    {
        $value = new User();
        $response = $this->statusUserInGame->addUser($value);

        $this->assertInstanceOf(StatusUserInGame::class, $response);
        $this->assertCount(1, $this->statusUserInGame->getUser());
        $this->assertTrue($this->statusUserInGame->getUser()->contains($value));

        $response = $this->statusUserInGame->removeUser($value);

        $this->assertInstanceOf(StatusUserInGame::class, $response);
        $this->assertCount(0, $this->statusUserInGame->getUser());
        $this->assertFalse($this->statusUserInGame->getUser()->contains($value));
    }

    public function testGame(): void
    {
        $value = new Game();
        $response = $this->statusUserInGame->addGame($value);

        $this->assertInstanceOf(StatusUserInGame::class, $response);
        $this->assertCount(1, $this->statusUserInGame->getGames());
        $this->assertTrue($this->statusUserInGame->getGames()->contains($value));

        $response = $this->statusUserInGame->removeGame($value);

        $this->assertInstanceOf(StatusUserInGame::class, $response);
        $this->assertCount(0, $this->statusUserInGame->getGames());
        $this->assertFalse($this->statusUserInGame->getGames()->contains($value));
    }

    public function testIsPresent(): void
    {
        $value = true;
        $this->statusUserInGame->setIsPresent($value);

        $this->assertIsBool($this->statusUserInGame->isIsPresent());
    }
}
