<?php

namespace App\Controller;

use App\Repository\GameRepository;
use App\Repository\UserRepository;
use App\Service\StatusService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ActivityController extends AbstractController
{
    private TranslatorInterface $translator;
    private GameRepository $repo_game;
    private UserRepository $user_repo;

    public function __construct(
        TranslatorInterface $translator,
        GameRepository $repo_game,
        UserRepository $user_repo
    ) {
        $this->translator = $translator;
        $this->repo_game = $repo_game;
        $this->user_repo = $user_repo;
    }

    #[Route('/activity', name: 'app_activity')]
    public function index(): Response
    {
        return $this->render('activity/index.html.twig', [
            'title' => $this->translator->trans('page.activity'),
            'games' => $this->repo_game->findAll(),
            'users' => $this->user_repo->findAll(),
            'allStatus' => [
                StatusService::NEW_GAME,
                StatusService::ACTIVE_GAME,
                StatusService::FINISH_GAME
            ],
            'weekSlot'=>[
                StatusService::SLOT_WEEK_PAIR,
                StatusService::SLOT_WEEK_ODD
            ],
            'hourSlot'=>[
                StatusService::SLOT_AFTERNOON,
                StatusService::SLOT_EVENING
            ]
        ]);
    }
}
