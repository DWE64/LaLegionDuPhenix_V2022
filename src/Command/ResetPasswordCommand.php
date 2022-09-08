<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:reset-password',
    description: 'reset manuel de password',
)]
class ResetPasswordCommand extends Command
{
    private $userRep;
    private $hash;

    public function __construct(
        UserRepository $userRep,
        UserPasswordHasherInterface $encoder)
    {
        parent::__construct();
        $this->userRep = $userRep;
        $this->hash = $encoder;
    }

    protected function configure(): void
    {
        $this
            ->setName('app:reset-password')
            ->addArgument('email', InputArgument::OPTIONAL, 'votre mail')
            ->addArgument('password', InputArgument::OPTIONAL, 'votre mot de passe')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $user = $this->userRep->findOneBy([
                                        'email'=>$email
                                       ]);
        if(in_array('ROLE_SUPER_ADMIN', $user->getRoles())){
            $io->error('Impossible de modifier le mot de passe admin');
            return Command::FAILURE;
        }
        if($password!=null){
            $passWdHash = $this->hash->hashPassword($user, $password);
            $user->setPassword($passWdHash);
        }else{
            $io->error('password manquant');
            return Command::FAILURE;
        }
        $this->userRep->add($user, true);

        $io->success('Mot de passe modifier avec succès');

        return Command::SUCCESS;
    }
}
