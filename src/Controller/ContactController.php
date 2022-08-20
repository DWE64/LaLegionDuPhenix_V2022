<?php

namespace App\Controller;

use App\Message\MailContact;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactController extends AbstractController
{
    private TranslatorInterface $translator;
    private MessageBusInterface $bus;

    public function __construct(TranslatorInterface $translator, MessageBusInterface $bus)
    {
        $this->translator = $translator;
        $this->bus = $bus;
    }

    #[Route('/contact', name: 'app_contact')]
    public function index(): Response
    {
        return $this->render(
            'contact/index.html.twig',
            [
                'title' => $this->translator->trans('page.contact'),
                'controller_name' => 'ContactController',
            ]
        );
    }

    #[Route('/contact/send', name: 'app_contact_send')]
    public function sendMessage(
        Request $request
    ): JsonResponse {
        if ($request->isXmlHttpRequest()) {
            if ($request->request->get('name')
                && $request->request->get('firstname')
                && $request->request->get('phone')
                && $request->request->get('email')
                && $request->request->get('subject')
                && $request->request->get('message')
            ) {
                $this->bus->dispatch(
                    new MailContact(
                        $request->request->get('email'),
                        $request->request->get('name'),
                        $request->request->get('firstname'),
                        $request->request->get('phone'),
                        $request->request->get('subject'),
                        $request->request->get('message')
                    )
                );
                $message = [
                    'message' => $this->translator->trans('page.contact.message_send_success')
                ];

                return new JsonResponse($message, Response::HTTP_OK, []);
            } else {
                $message = [
                    'message' => $this->translator->trans('page.contact.message_error_input_incomplete')
                ];

                return new JsonResponse($message, Response::HTTP_NO_CONTENT, []);
            }
        } else {
            $message = [
                'message' => Response::HTTP_BAD_REQUEST . ' - ' . $this->translator->trans(
                        'page.contact.message_error_async'
                    )
            ];
            return new JsonResponse($message, Response::HTTP_BAD_REQUEST, []);
        }
    }
}
