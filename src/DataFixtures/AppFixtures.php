<?php

namespace App\DataFixtures;

use App\Entity\Game;
use App\Entity\User;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use App\Service\StatusService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserRepository $userRep;
    private GameRepository $gameRep;
    private $hash;
    private $faker;

    public function __construct(UserRepository $userRep, UserPasswordHasherInterface $encoder, GameRepository $gameRep)
    {
        $this->userRep = $userRep;
        $this->gameRep = $gameRep;
        $this->hash = $encoder;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $roles[] = "ROLE_USER";
        for ($i =0; $i<30; $i++) {
            $user = new User();
            $user->setEmail($this->faker->email);
            $user->setRoles($roles);
            $passWdHash = $this->hash->hashPassword($user, 'password');
            $user->setPassword($passWdHash);
            $this->userRep->add($user, true);
        }

        $master = $this->userRep->find(1);
        for ($i =0; $i<30; $i++) {
            $game = new Game();
            $game->setTitle($this->faker->title);
            $game->setGameStatus(StatusService::NEW_GAME);
            $game->setGameMaster($master);
            $this->gameRep->add($game, true);
        }
    }
}
