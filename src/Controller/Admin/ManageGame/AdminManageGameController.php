<?php

namespace App\Controller\Admin\ManageGame;

use App\Entity\Game;
use App\Entity\GamePicture;
use App\Entity\StatusUserInGame;
use App\Entity\User;
use App\Repository\GameRepository;
use App\Repository\StatusUserInGameRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use App\Service\StatusService;
use DateTimeImmutable;
use JetBrains\PhpStorm\Deprecated;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_STAFF')]
class AdminManageGameController extends AbstractController
{
    private TranslatorInterface $translator;
    private GameRepository $repo_game;
    private UserRepository $user_repo;
    private FileUploader $fileUploader;
    private StatusUserInGameRepository $repo_status_user;

    public function __construct(TranslatorInterface $translator, GameRepository $repo_game, UserRepository $user_repo, FileUploader $fileUploader, StatusUserInGameRepository $repo_status_user)
    {
        $this->translator = $translator;
        $this->repo_game = $repo_game;
        $this->user_repo = $user_repo;
        $this->fileUploader = $fileUploader;
        $this->repo_status_user = $repo_status_user;
    }

    #[Route('/admin/manage/game', name: 'app_admin_manage_game')]
    public function index(): Response
    {
        return $this->render('admin/admin_manage_game/index.html.twig', [
            'title' => $this->translator->trans('page.admin.list_game'),
            'games' => $this->repo_game->findAll(),
            'users' => $this->user_repo->findAll(),
            'allStatus'=>[
                StatusService::NEW_GAME,
                StatusService::ACTIVE_GAME,
                StatusService::FINISH_GAME
            ]
        ]);
    }

    #[Route('/admin/manage/game/{gameId}/view', name: 'app_admin_manage_game_view')]
    public function viewGame(Request $request, Game $gameId): Response
    {
        $game = $this->repo_game->find($gameId);
        return $this->render('admin/admin_manage_game/_view.html.twig', [
            'title' => $this->translator->trans('page.admin.view_game'),
            'game' => $game,
            'users'=> $this->user_repo->findAll(),
            'allStatus'=>[
                StatusService::NEW_GAME,
                StatusService::ACTIVE_GAME,
                StatusService::FINISH_GAME
            ]
        ]);
    }

    #[Route('/admin/manage/game/add', name: 'app_admin_manage_game_add')]
    public function addGame(
        Request $request
    ): JsonResponse {
        if ($request->isXmlHttpRequest()) {
            $game = new Game();
            $date = new DateTimeImmutable('now');
            $message = [
                'createdAt' => $date->format('d/m/Y')
            ];
            if ($request->request->get('title')) {
                $game->setTitle($request->request->get('title'));
                $message += [
                    'title' => $game->getTitle()
                ];
            }
            if ($request->request->get('description')) {
                $game->setDescription($request->request->get('description'));
                $message += [
                    'description' => $game->getDescription()
                ];
            }
            if ($request->request->get('gameMaster')) {
                $user=$this->user_repo->find($request->request->get('gameMaster'));
                $statusUserInGame = new StatusUserInGame();
                $statusUserInGame->addUser($user);
                $statusUserInGame->setIsPresent(true);
                $game->addStatusUserInGame($statusUserInGame);
                $game->setGameMaster($user);
                $message += [
                    'gameMaster' => $game->getGameMaster()->getFirstname().' '.$game->getGameMaster()->getName()
                ];
            }
            if ($request->request->get('minGamePlace')) {
                $game->setMinGamePlace($request->request->get('minGamePlace'));
                $message += [
                    'minPlaceGame' => $game->getMinGamePlace()
                ];
            }
            if ($request->request->get('maxGamePlace')) {
                $game->setMaxGamePlace($request->request->get('maxGamePlace'));
                $message += [
                    'maxPlaceGame' => $game->getMaxGamePlace()
                ];
            }

            $game->setGameStatus(StatusService::NEW_GAME);
            $game->setAssignedPlace(0);

            $game->setCreatedAt($date);
            $this->repo_game->add($game, true);

            //=============================================
            //la ligne suivante servira quand j'aurai trouver un moyen de charger en dynamique les carte de jeux.
            //En attendant on renvoi une url et le fichier javascript redirige sur cette url
            $view=($game->getPicture()!=null)? 'admin/admin_manage_game/_template_game_card_with_picture.html.twig' : 'admin/admin_manage_game/_template_game_card_without_picture.html.twig';

            $message+=[
                'id' => $game->getId(),
                'view'=>$this->renderView($view,[
                    'game' => $game,
                    'users' => $this->user_repo->findAll(),
                    'allStatus'=>[
                        StatusService::NEW_GAME,
                        StatusService::ACTIVE_GAME,
                        StatusService::FINISH_GAME
                    ]
                ])
            ];

            return new JsonResponse($message, Response::HTTP_OK, []);
        } else {
            $message = Response::HTTP_NOT_MODIFIED;
            return new JsonResponse($message, Response::HTTP_NOT_MODIFIED, []);
        }
    }

    #[Route('/admin/manage/game/{gameId}/edit_picture', name: 'app_admin_manage_game_edit_picture')]
    public function editPictureGame(
        Request $request,
        Game $gameId
    ): JsonResponse {
        if ($request->isXmlHttpRequest()) {
            $game = $this->repo_game->find($gameId);
            $date = new DateTimeImmutable('now');
            $message = [
                'id' => $game->getId(),
                'updatedAt' => $date->format('d/m/Y')
            ];

            if ($request->files->get('picture')) {
                $nameFile = $this->fileUploader->uploadGamePicture($request->files->get('picture'));
                if($game->getPicture()===null){
                    $picture = new GamePicture();
                    $picture->setGame($game);
                    $picture->setGamePicture($nameFile);
                    $game->setPicture($picture);
                }else{
                    unlink($this->getParameter('game_picture').'/'.$game->getPicture()->getGamePicture());
                    $game->getPicture()->setGamePicture($nameFile);
                }

                $message += [
                    'picture' => $this->renderView('admin/admin_manage_game/_template_game_picture.html.twig',[
                        'game' => $game
                    ])
                ];
            }

            $game->setUpdatedAt($date);
            $this->repo_game->add($game, true);

            return new JsonResponse($message, Response::HTTP_OK, []);
        } else {
            $message = Response::HTTP_NOT_MODIFIED;
            return new JsonResponse($message, Response::HTTP_NOT_MODIFIED, []);
        }
    }

    #[Route('/admin/manage/game/{gameId}/edit', name: 'app_admin_manage_game_edit')]
    public function editGame(
        Request $request,
        Game $gameId
    ): JsonResponse {
        if ($request->isXmlHttpRequest()) {
            $game = $this->repo_game->find($gameId);
            $date = new DateTimeImmutable('now');
            $message = [
                'id' => $game->getId(),
                'updatedAt' => $date->format('d/m/Y')
            ];
            if ($request->request->get('title')) {
                $game->setTitle($request->request->get('title'));
                $message += [
                    'title' => $game->getTitle()
                ];
            }
            if ($request->request->get('description')) {
                $game->setDescription($request->request->get('description'));
                $message += [
                    'description' => $game->getDescription()
                ];
            }
            if ($request->request->get('gameMaster')) {
                foreach ($game->getStatusUserInGames() as $status){
                    foreach ($status->getUser() as $userStatus){
                        if($game->getGameMaster() === $userStatus){
                            $status->removeUser($game->getGameMaster());
                            $game->removeStatusUserInGame($status);
                            $this->repo_status_user->remove($status);
                        }
                    }
                }

                $user=$this->user_repo->find($request->request->get('gameMaster'));

                $statusUserInGame = new StatusUserInGame();
                $statusUserInGame->addUser($user);
                $statusUserInGame->setIsPresent(true);
                $game->addStatusUserInGame($statusUserInGame);
                $game->setGameMaster($user);
                $message += [
                    'gameMaster' => $game->getGameMaster()->getFirstname().' '.$game->getGameMaster()->getName()
                ];
            }
            if ($request->request->get('minGamePlace')) {
                $game->setMinGamePlace($request->request->get('minGamePlace'));
                $message += [
                    'minPlaceGame' => $game->getMinGamePlace()
                ];
            }
            if ($request->request->get('maxGamePlace')) {
                $game->setMaxGamePlace($request->request->get('maxGamePlace'));
                $message += [
                    'maxPlaceGame' => $game->getMaxGamePlace()
                ];
            }

            $game->setGameStatus(StatusService::NEW_GAME);
            $game->setAssignedPlace(0);

            $game->setUpdatedAt($date);
            $this->repo_game->add($game, true);

            return new JsonResponse($message, Response::HTTP_OK, []);
        } else {
            $message = Response::HTTP_NOT_MODIFIED;
            return new JsonResponse($message, Response::HTTP_NOT_MODIFIED, []);
        }
    }

    #[Route('/admin/manage/game/{gameId}/delete', name: 'app_admin_manage_game_delete', methods: [
        'DELETE'
    ])]
    public function deleteGame(Request $request, Game $gameId): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $game = $this->repo_game->find($gameId);
            $message=[
                'id' => $game->getId()
            ];
            if($game->getPicture()!==null){
                unlink($this->getParameter('game_picture').'/'.$game->getPicture()->getGamePicture());
            }

            $this->repo_game->remove($game, true);

            $message+=[
                'success' => $this->translator->trans('common.success.delete')
            ];
            return new JsonResponse($message, Response::HTTP_OK, []);
        } else {
            $message = Response::HTTP_NOT_MODIFIED;
            return new JsonResponse($message, Response::HTTP_NOT_MODIFIED, []);
        }
    }

    #[Route('/admin/manage/game/{gameId}/add_player', name: 'app_admin_manage_game_add_player')]
    public function addPlayerGame(
        Request $request,
        Game $gameId
    ): JsonResponse {

        if ($request->isXmlHttpRequest()) {
            $game = $this->repo_game->find($gameId);
            $date = new DateTimeImmutable('now');
            $message = [
                'id' => $game->getId(),
                'updatedAt' => $date->format('d/m/Y')
            ];

            if($game->getAssignedPlace() < $game->getMaxGamePlace()) {
                $placeAssigned = $game->getAssignedPlace();

                if ($request->request->get('player')) {
                    $user = $this->user_repo->find($request->request->get('player'));
                    $game->addPlayer($user);
                    $placeAssigned++;
                    $game->setAssignedPlace($placeAssigned);
                    $message += [
                        'player' => [
                            'id' => $user->getId(),
                            'firstname' => $user->getFirstname(),
                            'name' => $user->getName()
                        ],
                        'assignedPlace'=>$game->getAssignedPlace()
                    ];
                    $statusUserInGame = new StatusUserInGame();
                    $statusUserInGame->addUser($user);
                    $statusUserInGame->setIsPresent(false);
                    $game->addStatusUserInGame($statusUserInGame);
                }

                $game->setUpdatedAt($date);
                $this->repo_game->add($game, true);

                return new JsonResponse($message, Response::HTTP_OK, []);
            }else{
                $message +=[
                    'status' => Response::HTTP_NOT_MODIFIED,
                    'error' => 'all place assigned'
                ];
                return new JsonResponse($message, Response::HTTP_NOT_MODIFIED, []);
            }
        } else {
            $message = Response::HTTP_NOT_MODIFIED;
            return new JsonResponse($message, Response::HTTP_NOT_MODIFIED, []);
        }
    }

    #[Route('/admin/manage/game/{gameId}/get_players', name: 'app_admin_manage_game_get_player', methods: [
        'GET'
    ])]
    public function getPlayerGame(
        Request $request,
        Game $gameId
    ): JsonResponse {
        if ($request->isXmlHttpRequest()) {
            $game = $this->repo_game->find($gameId);
            $message=[
                'id' => $game->getId(),
                'view'=>$this->renderView('admin/admin_manage_game/_template_delete_player_modal.html.twig',[
                    'game' => $game
                ])
            ];

            return new JsonResponse($message, Response::HTTP_OK, []);
        } else {
            $message = Response::HTTP_NOT_MODIFIED;
            return new JsonResponse($message, Response::HTTP_NOT_MODIFIED, []);
        }
    }

    #[Route('/admin/manage/game/{gameId}/delete_player', name: 'app_admin_manage_game_delete_player', methods: [
        'DELETE'
    ])]
    public function deletePlayerGame(
        Request $request,
        Game $gameId
    ): JsonResponse {
        if ($request->isXmlHttpRequest()) {
            $game = $this->repo_game->find($gameId);
            $date = new DateTimeImmutable('now');
            $message = [
                'id' => $game->getId(),
                'updatedAt' => $date->format('d/m/Y')
            ];
            if($game->getAssignedPlace() > 0) {
                $placeAssigned = $game->getAssignedPlace();

                if ($request->request->get('player')) {
                    $user = $this->user_repo->find($request->request->get('player'));
                    $game->removePlayer($user);
                    $placeAssigned--;
                    $game->setAssignedPlace($placeAssigned);
                    $message += [
                        'player' => [
                            'id' => $user->getId(),
                            'firstname' => $user->getFirstname(),
                            'name' => $user->getName()
                        ],
                        'assignedPlace'=>$game->getAssignedPlace()
                    ];
                    foreach ($game->getStatusUserInGames() as $status){
                        foreach ($status->getUser() as $userStatus){
                            if($user === $userStatus){
                                $status->removeUser($user);
                                $game->removeStatusUserInGame($status);
                                $this->repo_status_user->remove($status);
                            }
                        }
                    }
                    $game->setUpdatedAt($date);
                    $this->repo_game->add($game, true);

                    return new JsonResponse($message, Response::HTTP_OK, []);
                }

            }else{
                $message +=[
                    'status' => Response::HTTP_NOT_MODIFIED,
                    'error' => 'never place assigned'
                ];
                return new JsonResponse($message, Response::HTTP_NOT_MODIFIED, []);
            }
        } else {
            $message = Response::HTTP_NOT_MODIFIED;
            return new JsonResponse($message, Response::HTTP_NOT_MODIFIED, []);
        }
    }

    #[Route('/admin/manage/game/{gameId}/change_status', name: 'app_admin_manage_game_change_status')]
    public function changeStatusGame(
        Request $request,
        Game $gameId
    ): JsonResponse {
        if ($request->isXmlHttpRequest()) {
            $game = $this->repo_game->find($gameId);
            $date = new DateTimeImmutable('now');
            $message = [
                'id' => $game->getId(),
                'updatedAt' => $date->format('d/m/Y')
            ];
            if ($request->request->get('statusGame')) {
                if($request->request->get('statusGame')===StatusService::NEW_GAME){
                    $game->setGameStatus(StatusService::NEW_GAME);
                }
                if($request->request->get('statusGame')===StatusService::ACTIVE_GAME){
                    $game->setGameStatus(StatusService::ACTIVE_GAME);
                }
                if($request->request->get('statusGame')===StatusService::FINISH_GAME){
                    $game->setGameStatus(StatusService::FINISH_GAME);
                }

                $message += [
                    'status' => $game->getGameStatus()
                ];
            }

            $game->setUpdatedAt($date);
            $this->repo_game->add($game, true);

            return new JsonResponse($message, Response::HTTP_OK, []);
        } else {
            $message = Response::HTTP_NOT_MODIFIED;
            return new JsonResponse($message, Response::HTTP_NOT_MODIFIED, []);
        }
    }

    #[Route('/admin/manage/game/{gameId}/get_player/{userId}', name: 'app_admin_manage_game_get_player_status', methods: [
        'GET'
    ])]
    #[Deprecated]
    public function getPlayerWithStatusGame(
        Request $request,
        Game $gameId,
        User $userId
    ): JsonResponse {
        if ($request->isXmlHttpRequest()) {
            $game = $this->repo_game->find($gameId);
            $user = $this->user_repo->find($userId);
            foreach ($game->getStatusUserInGames() as $status){
                foreach ($status->getUser() as $player){
                    if($player === $user){
                        $message=[
                            'id'=>$game->getId(),
                            'idStatus'=>$status->getId(),
                            'isPresent'=>$status->isIsPresent()
                        ];
                    }
                }
            }
            return new JsonResponse($message, Response::HTTP_OK, []);
        } else {
            $message = Response::HTTP_NOT_MODIFIED;
            return new JsonResponse($message, Response::HTTP_NOT_MODIFIED, []);
        }
    }

    #[Route('/admin/manage/game/change_status_player/{idStatus}', name: 'app_admin_manage_user_change_status_game')]
    public function changePlayersStatusGame(
        Request $request,
        StatusUserInGame $idStatus
    ): JsonResponse {
        if ($request->isXmlHttpRequest()) {
            $status = $this->repo_status_user->find($idStatus);

            if($status->isIsPresent()){
                $status->setIsPresent(false);
            }else{
                $status->setIsPresent(true);
            }

            $this->repo_status_user->add($status, true);

            return new JsonResponse('', Response::HTTP_OK, []);
        } else {
            $message = Response::HTTP_NOT_MODIFIED;
            return new JsonResponse($message, Response::HTTP_NOT_MODIFIED, []);
        }
    }



}