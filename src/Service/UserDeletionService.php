<?php
namespace App\Service;

use App\Entity\User;
use App\Entity\Game;
use App\Repository\GameRepository;
use App\Repository\StatusUserInGameRepository;
use App\Repository\UserRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserDeletionService
{
    private string $gamePictureDirectory;
    private string $userPictureDirectory;
    private UserRepository $userRepository;
    private GameRepository $gameRepository;
    private StatusUserInGameRepository $statusUserInGameRepository;
    private TranslatorInterface $translator;

    public function __construct(
        $gamePictureDirectory,
        $userPictureDirectory,
        UserRepository $userRepository,
        GameRepository $gameRepository,
        StatusUserInGameRepository $statusUserInGameRepository,
        TranslatorInterface $translator
    ) {
        $this->gamePictureDirectory = $gamePictureDirectory;
        $this->userPictureDirectory = $userPictureDirectory;
        $this->userRepository = $userRepository;
        $this->gameRepository = $gameRepository;
        $this->statusUserInGameRepository = $statusUserInGameRepository;
        $this->translator = $translator;
    }

    public function removeUserFromGames(User $user): void
    {
        $statusUserInGames = $this->statusUserInGameRepository->findAll();

        foreach ($statusUserInGames as $statusUserInGame) {
            $games = $statusUserInGame->getGames();

            foreach ($games as $game) {
                $game->removePlayer($user);
                $assignedPlace = $game->getAssignedPlace();
                $game->setAssignedPlace(max(0, $assignedPlace - 1));
                $this->gameRepository->add($game, true);
            }
            $this->statusUserInGameRepository->remove($statusUserInGame, true);
        }
    }

    public function deleteGamesWhereUserIsMaster(User $user): void
    {
        $games = $this->gameRepository->findBy(['gameMaster' => $user]);

        foreach ($games as $game) {
            $this->deleteGame($game);
        }
    }

    public function deleteUser(User $user): void
    {
        if ($user->getProfilPicture() !== null) {
            unlink($this->getUserProfilPicturePath(). '/' . $user->getProfilPicture()->getProfilPicture());
        }
        $this->removeUserFromGames($user);
        $this->deleteGamesWhereUserIsMaster($user);
        $this->userRepository->remove($user, true);
    }

    private function deleteGame(Game $game): void
    {
        if ($game->getPicture() !== null) {
            unlink($this->getGamePicturePath() . '/' . $game->getPicture()->getGamePicture());
        }

        $this->gameRepository->remove($game, true);
    }

    public function getGamePicturePath(): string
    {
        return $this->gamePictureDirectory;
    }

    public function getUserProfilPicturePath(): string
    {
        return $this->userPictureDirectory;
    }
}


