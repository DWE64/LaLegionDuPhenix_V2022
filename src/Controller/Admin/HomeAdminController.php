<?php

namespace App\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_STAFF')]
class HomeAdminController extends AbstractController
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    #[Route('/home/admin', name: 'app_home_admin')]
    public function index(): Response
    {
        return $this->render('admin/home_admin/view.html.twig', [
            'title' => $this->translator->trans('page.admin.home')
        ]);
    }
}
