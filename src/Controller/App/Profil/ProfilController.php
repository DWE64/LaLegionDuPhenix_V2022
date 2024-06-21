<?php

namespace App\Controller\App\Profil;

use App\Entity\Game;
use App\Entity\ProfilPicture;
use App\Entity\StatusUserInGame;
use App\Entity\User;
use App\Repository\GameRepository;
use App\Repository\StatusUserInGameRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_USER')]
class ProfilController extends AbstractController
{
    private TranslatorInterface $translator;
    private UserRepository $userRepository;
    private FileUploader $fileUploader;
    private StatusUserInGameRepository $repo_status_user;
    private GameRepository $gameRepo;
    private EntityManagerInterface $em;

    public function __construct(
        TranslatorInterface $translator,
        UserRepository $userRepository,
        FileUploader $fileUploader,
        StatusUserInGameRepository $repo_status_user,
        GameRepository $gameRepo,
        EntityManagerInterface $em
    ) {
        $this->translator = $translator;
        $this->userRepository = $userRepository;
        $this->fileUploader = $fileUploader;
        $this->repo_status_user = $repo_status_user;
        $this->gameRepo = $gameRepo;
        $this->em = $em;
    }

    #[Route('/profil', name: 'app_profil')]
    public function index(): Response
    {
        $user = $this->getUser();
        $baseTemplate = ($this->isGranted('ROLE_STAFF')) ? 'admin/base.html.twig' : 'app/base.html.twig';
        return $this->render(
            'app/profil/index.html.twig',
            [
                'title' => $this->translator->trans('page.app.profil_user'),
                'baseTemplate' => $baseTemplate,
                'user' => $user
            ]
        );
    }

    #[Route('/profil/edit/{id}', name: 'app_profil_user_edit', methods: ['GET', 'POST', 'UPDATE'])]
    public function editUser(Request $request, User $user): JsonResponse {
        if ($request->isXmlHttpRequest()) {
            $date = new DateTimeImmutable('now');
            $message = [
                'id' => $user->getId(),
                'updatedAt' => $date->format('d/m/Y')
            ];
            if ($request->request->get('name')) {
                $user->setName($request->request->get('name'));
                $message += [
                    'name' => $user->getName()
                ];
            }
            if ($request->request->get('firstname')) {
                $user->setFirstname($request->request->get('firstname'));
                $message += [
                    'firstname' => $user->getFirstname()
                ];
            }
            if ($request->request->get('username')) {
                $user->setUsername($request->request->get('username')); // Correction ici
                $message += [
                    'username' => $user->getUsername()
                ];
            }
            if ($request->request->get('birthday')) {
                $birthday = new DateTime($request->request->get('birthday'));
                $user->setBirthday($birthday);
                $message += [
                    'birthday' => $user->getBirthday()->format('d/m/Y')
                ];
            }
            if ($request->request->get('address')) {
                $user->setAddress($request->request->get('address'));
                $message += [
                    'address' => $user->getAddress()
                ];
            }
            if ($request->request->get('city')) {
                $user->setCity($request->request->get('city'));
                $message += [
                    'city' => $user->getCity()
                ];
            }
            if ($request->request->get('zip')) {
                $user->setPostalCode($request->request->get('zip'));
                $message += [
                    'zip' => $user->getPostalCode()
                ];
            }

            $user->setUpdatedAt($date);
            $this->userRepository->add($user, true);

            return new JsonResponse($message, Response::HTTP_OK, []);
        } else {
            $message = Response::HTTP_NOT_MODIFIED;
            return new JsonResponse($message, Response::HTTP_NOT_MODIFIED, []);
        }
    }

    #[Route('/profil/edit/{id}/edit_picture', name: 'app_profil_user_edit_picture')]
    public function editPictureUser(
        Request $request,
        User $id
    ): JsonResponse {
        if ($request->isXmlHttpRequest()) {
            $user = $this->userRepository->find($id);
            $date = new DateTimeImmutable('now');
            $message = [
                'id' => $user->getId(),
                'updatedAt' => $date->format('d/m/Y')
            ];

            if ($request->files->get('picture')) {
                $nameFile = $this->fileUploader->uploadUserPicture($request->files->get('picture'));
                if ($user->getProfilPicture() === null) {
                    $picture = new ProfilPicture();
                    $picture->setUser($user);
                    $picture->setProfilPicture($nameFile);
                    $user->setProfilPicture($picture);
                } else {
                    unlink(
                        $this->getParameter('user_picture_profil') . '/' . $user->getProfilPicture()->getProfilPicture()
                    );
                    $user->getProfilPicture()->setProfilPicture($nameFile);
                }

                $message += [
                    'picture' => $this->renderView(
                        'app/profil/_template_picture_profil.html.twig',
                        [
                            'user' => $user
                        ]
                    )
                ];
            }

            $user->setUpdatedAt($date);
            $this->userRepository->add($user, true);

            return new JsonResponse($message, Response::HTTP_OK, []);
        } else {
            $message = Response::HTTP_NOT_MODIFIED;
            return new JsonResponse($message, Response::HTTP_NOT_MODIFIED, []);
        }
    }

    #[Route('/profil/edit/game/player_or_master/change_status/{idStatus}', name: 'app_profil_user_change_status_player_or_master_game')]
    public function changePlayersOrMasterStatusGame(Request $request, StatusUserInGame $idStatus): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $status = $this->repo_status_user->find($idStatus);

            if (!$status) {
                return new JsonResponse(['error' => 'Status not found'], Response::HTTP_NOT_FOUND);
            }

            $newStatus = $request->request->get('status');
            $playerId = $request->request->get('playerid');

            // Debugging

            $status->setIsPresent(json_decode($newStatus));

            $this->em->persist($status);
            $this->em->flush();

            // Check if the status is updated
            $updatedStatus = $this->repo_status_user->find($idStatus);


            $response = [
                'playerid' => $playerId,
                'status' => $newStatus
            ];
            return new JsonResponse($response, Response::HTTP_OK);
        } else {
            $message = Response::HTTP_NOT_MODIFIED;
            return new JsonResponse($message, Response::HTTP_NOT_MODIFIED);
        }
    }


    #[Route('/profil/edit/game/{idGame}/master/post_message', name: 'app_profil_user_post_message_game_master')]
    public function postMessageGameMaster(
        Request $request,
        Game $idGame
    ): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            $game = $this->gameRepo->find($idGame);

            $date = new DateTimeImmutable('now');
            $message = [
                'id' => $game->getId(),
                'updatedAt' => $date->format('d/m/Y')
            ];
            if ($request->request->get('message_master')) {
                $game->setGameMasterCommentary($request->request->get('message_master'));
                $message += [
                    'message_master' => $game->getGameMasterCommentary()
                ];
            }

            $game->setUpdatedAt($date);
            $this->gameRepo->add($game, true);

            return new JsonResponse($message, Response::HTTP_OK, []);
        } else {
            $message = Response::HTTP_NOT_MODIFIED;
            return new JsonResponse($message, Response::HTTP_NOT_MODIFIED, []);
        }
    }
}
