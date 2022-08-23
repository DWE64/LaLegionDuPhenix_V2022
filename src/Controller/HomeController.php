<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomeController extends AbstractController
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'title' => $this->translator->trans('page.home')
        ]);
    }

    #[Route('/change_locale/{locale}', name:'change_locale')]
    public function changeLocale($locale, Request $request)
    {
        $request->getSession()->set('_locale',$locale);

        return $this->redirect($request->headers->get('referer'));
    }
}
