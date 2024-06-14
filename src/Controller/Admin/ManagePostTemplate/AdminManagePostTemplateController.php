<?php

namespace App\Controller\Admin\ManagePostTemplate;

use App\Entity\PostTemplate;
use App\Form\PostTemplateType;
use App\Repository\PostTemplateRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_STAFF')]
#[Route('/admin/manage/post/template')]
class AdminManagePostTemplateController extends AbstractController
{
    private TranslatorInterface $translator;
    private PostTemplateRepository $postTemplateRepository;

    public function __construct(
        TranslatorInterface $translator,
        PostTemplateRepository $postTemplateRepository
    ) {
        $this->translator = $translator;
        $this->postTemplateRepository = $postTemplateRepository;
    }

    #[Route('/', name: 'app_admin_manage_post_template_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/admin_manage_post_template/index.html.twig', [
            'title'=> $this->translator->trans('page.admin.list_post_template'),
            'post_templates' => $this->postTemplateRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_manage_post_template_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $postTemplate = new PostTemplate();
        $form = $this->createForm(PostTemplateType::class, $postTemplate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->postTemplateRepository->add($postTemplate, true);

            return $this->redirectToRoute('app_admin_manage_post_template_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/admin_manage_post_template/new.html.twig', [
            'title' => $this->translator->trans('page.admin.new_post_template'),
            'post_template' => $postTemplate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_manage_post_template_show', methods: ['GET'])]
    public function show(PostTemplate $postTemplate): Response
    {
        return $this->render('admin/admin_manage_post_template/show.html.twig', [
            'title' => $this->translator->trans('page.admin.post_template_detail'),
            'post_template' => $postTemplate,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_manage_post_template_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PostTemplate $postTemplate): Response
    {
        $form = $this->createForm(PostTemplateType::class, $postTemplate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $postTemplate->setUpdatedAt(new DateTimeImmutable('now'));
            $this->postTemplateRepository->add($postTemplate, true);

            return $this->redirectToRoute('app_admin_manage_post_template_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/admin_manage_post_template/edit.html.twig', [
            'title' => $this->translator->trans('page.admin.post_template_edit'),
            'post_template' => $postTemplate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_manage_post_template_delete', methods: ['POST'])]
    public function delete(Request $request, PostTemplate $postTemplate): Response
    {
        if ($this->isCsrfTokenValid('delete'.$postTemplate->getId(), $request->request->get('_token'))) {
            $this->postTemplateRepository->remove($postTemplate, true);
        }

        return $this->redirectToRoute('app_admin_manage_post_template_index', [], Response::HTTP_SEE_OTHER);
    }
}
