<?php

namespace App\Tests;

use App\Entity\Game;
use App\Entity\StatusUserInGame;
use App\Entity\User;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    private Game $game;

    protected function setUp(): void
    {
        parent::setUp();
        $this->game = new Game();
    }

    public function testTitle(): void
    {
        $value='mon titre';
        $response = $this->game->setTitle($value);

        $this->assertEquals($value, $this->game->getTitle());
        $this->assertInstanceOf(Game::class, $response);
    }
    public function testDescription(): void
    {
        $value='xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
        $response = $this->game->setDescription($value);

        $this->assertInstanceOf(Game::class, $response);
        $this->assertEquals($value, $this->game->getDescription());
    }
    public function testMinGamePlace(): void
    {
        $value=1;
        $response = $this->game->setMinGamePlace($value);

        $this->assertInstanceOf(Game::class, $response);
        $this->assertIsInt($this->game->getMinGamePlace());
        $this->assertEquals($value, $this->game->getMinGamePlace());
    }
    public function testMaxGamePlace(): void
    {
        $value=1;
        $response = $this->game->setMaxGamePlace($value);

        $this->assertInstanceOf(Game::class, $response);
        $this->assertIsInt($this->game->getMaxGamePlace());
        $this->assertEquals($value, $this->game->getMaxGamePlace());
    }
    public function testAssignedPlace(): void
    {
        $value=1;
        $response = $this->game->setAssignedPlace($value);

        $this->assertInstanceOf(Game::class, $response);
        $this->assertIsInt($this->game->getAssignedPlace());
        $this->assertEquals($value, $this->game->getAssignedPlace());
    }
    public function testGameStatus(): void
    {
        $value='NEW';
        $response = $this->game->setGameStatus($value);

        $this->assertInstanceOf(Game::class, $response);
        $this->assertEquals($value, $this->game->getGameStatus());
    }
    public function testGameMasterCommentary(): void
    {
        $value='ceci est un commentaire';
        $response = $this->game->setGameMasterCommentary($value);

        $this->assertInstanceOf(Game::class, $response);
        $this->assertEquals($value, $this->game->getGameMasterCommentary());
    }

    /**
     * @throws \Exception
     */
    public function testCreatedAt(): void
    {
        $value = new DateTimeImmutable(date('d-m-Y H:i:s'));
        $response = $this->game->setCreatedAt($value);


        $this->assertInstanceOf(Game::class, $response);
        $this->assertEquals($value, $this->game->getCreatedAt());
    }

    /**
     * @throws \Exception
     */
    public function testUpdatedAt(): void
    {
        $value = new DateTimeImmutable(date('d-m-Y H:i:s'));
        $response = $this->game->setUpdatedAt($value);


        $this->assertInstanceOf(Game::class, $response);
        $this->assertEquals($value, $this->game->getUpdatedAt());
    }
    public function testGameMaster(): void
    {
        $value= new User();
        $response = $this->game->setGameMaster($value);

        $this->assertInstanceOf(Game::class, $response);
        $this->assertInstanceOf(User::class, $this->game->getGameMaster());
    }
    public function testPlayers(): void
    {
        $value = new User();
        $response = $this->game->addPlayer($value);

        $this->assertInstanceOf(Game::class, $response);
        $this->assertCount(1, $this->game->getPlayers());
        $this->assertTrue($this->game->getPlayers()->contains($value));

        $response = $this->game->removePlayer($value);

        $this->assertInstanceOf(Game::class, $response);
        $this->assertCount(0, $this->game->getPlayers());
        $this->assertFalse($this->game->getPlayers()->contains($value));
    }
    public function testStatusUserInGame(): void
    {
        $value = new StatusUserInGame();
        $response = $this->game->addStatusUserInGame($value);

        $this->assertInstanceOf(Game::class, $response);
        $this->assertCount(1, $this->game->getStatusUserInGames());
        $this->assertTrue($this->game->getStatusUserInGames()->contains($value));

        $response = $this->game->removeStatusUserInGame($value);

        $this->assertInstanceOf(Game::class, $response);
        $this->assertCount(0, $this->game->getStatusUserInGames());
        $this->assertFalse($this->game->getStatusUserInGames()->contains($value));
    }
}
