<?php

namespace App\Controller\Admin\ManageUser;

use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_BUREAU')]
class AdminManageUserController extends AbstractController
{
    private TranslatorInterface $translator;
    private UserRepository $userRepository;

    public function __construct(TranslatorInterface $translator, UserRepository $userRepository)
    {
        $this->translator = $translator;
        $this->userRepository = $userRepository;
    }

    #[Route('/admin/manage/user', name: 'app_admin_manage_user')]
    public function index(): Response
    {
        return $this->render('admin/admin_manage_user/index.html.twig', [
            'title' => $this->translator->trans('page.admin.list_user'),
            'users' => $this->userRepository->findAll()
        ]);
    }

}
