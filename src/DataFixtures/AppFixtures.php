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
        $rolesAdmin[] = "ROLE_USER";
        $rolesAdmin[] = "ROLE_SUPER_ADMIN";
        $user = new User();
        $user->setEmail('q.perriere@gmail.com');
        $user->setRoles($rolesAdmin);
        $user->setName('Perriere');
        $user->setFirstname('Quentin');
        $user->setUsername($this->faker->userName);
        $user->setAddress($this->faker->address);
        $user->setBirthday($this->faker->dateTime);
        $user->setCity($this->faker->city);
        $user->setPostalCode($this->faker->postcode);
        $passWdHash = $this->hash->hashPassword($user, '123456');
        $user->setPassword($passWdHash);
        $this->userRep->add($user, true);

        $user = new User();
        $user->setEmail('contact@dwe64.com');
        $user->setRoles($rolesAdmin);
        $user->setName($this->faker->name);
        $user->setFirstname($this->faker->firstName);
        $user->setUsername($this->faker->userName);
        $user->setAddress($this->faker->address);
        $user->setBirthday($this->faker->dateTime);
        $user->setCity($this->faker->city);
        $user->setPostalCode($this->faker->postcode);
        $passWdHash = $this->hash->hashPassword($user, 'DevWebEnt64');
        $user->setPassword($passWdHash);
        $this->userRep->add($user, true);

        $roles[] = "ROLE_USER";
        for ($i =0; $i<30; $i++) {
            $user = new User();
            $user->setEmail($this->faker->email);
            $user->setName($this->faker->name);
            $user->setFirstname($this->faker->firstName);
            $user->setUsername($this->faker->userName);
            $user->setAddress($this->faker->address);
            $user->setBirthday($this->faker->dateTime);
            $user->setCity($this->faker->city);
            $user->setPostalCode($this->faker->postcode);
            $user->setRoles($roles);
            $passWdHash = $this->hash->hashPassword($user, 'password');
            $user->setPassword($passWdHash);
            $this->userRep->add($user, true);
        }

        $master = $this->userRep->find(1);
        for ($i =0; $i<5; $i++) {
            $game = new Game();
            $game->setTitle($this->faker->title);
            $game->setGameStatus(StatusService::NEW_GAME);
            $game->setGameMaster($master);
            $this->gameRep->add($game, true);
        }
    }
}
