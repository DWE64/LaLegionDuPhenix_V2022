<?php

namespace App\Command;

use App\Message\MailRefreshStatusGame;
use App\Repository\GameRepository;
use App\Repository\StatusUserInGameRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;

#[AsCommand(
    name: 'app:reset:players-game-status',
    description: 'Reset all players games status',
)]
class ResetPlayersGameStatusCommand extends Command
{
    private StatusUserInGameRepository $status_repo;
    private GameRepository $game_repo;
    private MessageBusInterface $bus;
    private RouterInterface $router;

    public function __construct(
        StatusUserInGameRepository $status_repo,
        GameRepository $game_repo,
        MessageBusInterface $bus,
        RouterInterface $router
    ) {
        parent::__construct();
        $this->status_repo = $status_repo;
        $this->game_repo = $game_repo;
        $this->bus = $bus;
        $this->router = $router;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $listStatus = $this->status_repo->findAll();
        $games = $this->game_repo->findAll();
        $this->router->getContext()
            ->setHost('lalegionduphenix.com')
            ->setScheme('https')
            ->setBaseUrl('https://www.lalegionduphenix.com');


        if (empty($listStatus)) {
            $io->error('no status for games found');
            return Command::FAILURE;
        }

        foreach ($listStatus as $status) {
            if ($status->isIsPresent()) {
                $status->setIsPresent(false);
                $this->status_repo->add($status,true);
            }
        }

        if (empty($games)) {
            $io->error('no games found');
            return Command::FAILURE;
        }

        foreach ($games as $game) {
            $this->bus->dispatch(
                new MailRefreshStatusGame(
                    $game->getGameMaster()->getEmail(),
                    $game->getGameMaster()->getName(),
                    $game->getGameMaster()->getFirstname(),
                    $this->router->generate('app_profil'),
                    $game->getTitle()
                )
            );

            if(!empty($game->getPlayers())){
                foreach ($game->getPlayers() as $player){
                    $this->bus->dispatch(
                        new MailRefreshStatusGame(
                            $player->getEmail(),
                            $player->getName(),
                            $player->getFirstname(),
                            $this->router->generate('app_profil'),
                            $game->getTitle()
                        )
                    );
                }
            }else{
                $io->error('no players for games found');
                return Command::FAILURE;
            }
        }

        $io->success('All players and game master status are resetting.');

        return Command::SUCCESS;
    }
}
