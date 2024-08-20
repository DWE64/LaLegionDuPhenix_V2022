<?php

namespace App\Controller\Admin\ManageUser;

use App\Entity\User;
use App\Message\MailRemoveInscription;
use App\Message\MailUpdateUserByAdmin;
use App\Message\MailValidationInscription;
use App\Repository\UserRepository;
use App\Service\StatusService;
use App\Service\UserDeletionService;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Annotations\AnnotationReader;
use JetBrains\PhpStorm\Deprecated;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\SerializerInterface;



class AdminManageUserController extends AbstractController
{
    private TranslatorInterface $translator;
    private UserRepository $userRepository;
    private MessageBusInterface $bus;
    private UserDeletionService $userDeletionService;
    private SerializerInterface $serializer;

    public function __construct(
        TranslatorInterface $translator,
        MessageBusInterface $bus,
        UserDeletionService $userDeletionService,
        UserRepository $userRepository,
        SerializerInterface $serializer
    ) {
        $this->translator = $translator;
        $this->userRepository = $userRepository;
        $this->bus = $bus;
        $this->userDeletionService = $userDeletionService;
        $this->serializer = $serializer;
    }

    #[Route('/admin/manage/user', name: 'app_admin_manage_user')]
    #[IsGranted('ROLE_STAFF')]
    public function index(): Response
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $roleUser = 'ROLE_USER';

        if (in_array('ROLE_SUPER_ADMIN', $roles)) {
            $roleUser = 'ROLE_SUPER_ADMIN';
        }
        if (in_array('ROLE_STAFF', $roles)) {
            $roleUser = 'ROLE_STAFF';
        }

        return $this->render(
            'admin/admin_manage_user/test.html.twig',
            [
                'title' => $this->translator->trans('page.admin.list_user'),
                'roleUser' => $roleUser,
                'users' => $this->userRepository->findAll(),
                'seniorityStatus' => [
                    StatusService::MEMBER_NEW,
                    StatusService::MEMBER_OLD
                ],
                'allRoles' => [
                    'ROLE_PLAYER',
                    'ROLE_GAMEMASTER',
                    'ROLE_MEMBER_REPRESENT',
                    'ROLE_STAFF'
                ]
            ]
        );
    }

    #[Route('/api/admin/manage/users', name: 'api_admin_manage_users', methods: ['GET'])]
    public function getUsers(Request $request): JsonResponse
    {
        $role = $request->query->get('role');
        if ($role !== 'ROLE_STAFF' && $role !== 'ROLE_SUPER_ADMIN') {
            return new JsonResponse(['error' => 'Accès refusé !'], Response::HTTP_FORBIDDEN);
        }

        $resultUsers = $this->userRepository->findAll();
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $datetimeNormalizer = new DateTimeNormalizer([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s']); // Format de date personnalisé
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$datetimeNormalizer, $normalizer]);

        $users = $serializer->normalize($resultUsers, null, ['groups' => 'admin_manage_user']);


        $data = [
            'listUsers' => $users,
            'seniorityStatus' => [
                StatusService::MEMBER_NEW,
                StatusService::MEMBER_OLD
            ],
            'allRoles' => [
                'ROLE_PLAYER',
                'ROLE_GAMEMASTER',
                'ROLE_MEMBER_REPRESENT',
                'ROLE_STAFF'
            ]
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/api/admin/manager/user/{id}/change_status_is_association_member',
        name: 'app_admin_manage_user_change_status_member_association',
        methods: [
            'GET',
            'POST',
            'PUT'
        ]
    )]
    public function changeStatusMemberAssociation(
        Request $request,
        User $user
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_STAFF');
        $date = new DateTimeImmutable('now');

        if ($user->isIsAssociationMember()) {
            $user->setIsAssociationMember(false);
            $user->setMemberStatus(StatusService::MEMBER_NOT_REGISTER);
            $user->setAssociationRegistrationDate(null);
            $urlContact = $request->getSchemeAndHttpHost() . $this->generateUrl('app_contact');
            $this->bus->dispatch(
                new MailRemoveInscription(
                    $user->getEmail(),
                    $user->getName(),
                    $user->getFirstname(),
                    $urlContact
                )
            );
        } else {
            $user->setIsAssociationMember(true);
            $registrationDate = new DateTime('now');
            $user->setMemberStatus(StatusService::MEMBER_REGISTER);
            $user->setAssociationRegistrationDate($registrationDate);
            $urlProfil = $request->getSchemeAndHttpHost() . $this->generateUrl('app_profil');
            $this->bus->dispatch(
                new MailValidationInscription(
                    $user->getEmail(),
                    $user->getName(),
                    $user->getFirstname(),
                    $urlProfil
                )
            );
        }

        $user->setUpdatedAt($date);
        $this->userRepository->add($user, true);

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $datetimeNormalizer = new DateTimeNormalizer([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s']); // Format de date personnalisé
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$datetimeNormalizer, $normalizer]);

        $data = $serializer->normalize($user, null, ['groups' => 'admin_manage_user']);

        return new JsonResponse($data, Response::HTTP_OK, []);
    }

    #[Route('/api/admin/manager/user/{id}/checked_oldest',
        name: 'app_admin_manage_user_checked_oldest',
        methods: [
            'GET'
        ]
    )]
    #[Deprecated]
    public function checkedOldest(
        Request $request,
        User $user
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_STAFF');
        if ($user->getMemberSeniority() != null) {
            $arrayStatus = ($user->getMemberSeniority(
                ) === StatusService::MEMBER_NEW) ? [StatusService::MEMBER_OLD] : [StatusService::MEMBER_NEW];
        } else {
            $arrayStatus = [
                StatusService::MEMBER_NEW,
                StatusService::MEMBER_OLD
            ];
        }
        $message = [
            'id' => $user->getId(),
            'seniority' => $arrayStatus
        ];

        return new JsonResponse($message, Response::HTTP_OK, []);
    }

    #[Route('/api/admin/manager/user/{id}/edit',
        name: 'app_admin_manage_user_edit',
        methods: [
            'GET',
            'POST',
            'UPDATE',
            'PUT'
        ]
    )]
    public function editUser(
        Request $request,
        User $user
    ): JsonResponse {

        $this->denyAccessUnlessGranted('ROLE_STAFF');
        $date = new DateTimeImmutable('now');
        $userSend = $this->serializer->deserialize($request->getContent(), User::class, 'json');

        $user->setName($userSend->getName());
        $user->setFirstname($userSend->getFirstname());

        $userRole = $user->getRoles();
        $authorization = true;

        foreach ($userRole as $role) {
            if ($role === "ROLE_SUPER_ADMIN") {
                $authorization = false;
                break;
            }
        }
        if (!$authorization) {
            $message = "assign role forbidden, impossible to change role SUPER ADMIN";
            return new JsonResponse($message, Response::HTTP_FORBIDDEN, []);
        } else {
            $user->setRoles($userSend->getRoles());
        }

        $user->setUsername($userSend->getUsername());
        $user->setBirthday($userSend->getBirthday());
        $user->setAddress($userSend->getAddress());
        $user->setCity($userSend->getCity());
        $user->setPostalCode($userSend->getPostalCode());

        $status = ($userSend->getMemberSeniority() === StatusService::MEMBER_NEW) ? StatusService::MEMBER_NEW : StatusService::MEMBER_OLD;
        $user->setMemberSeniority($status);

        $user->setUpdatedAt($date);
        $this->userRepository->add($user, true);

        $urlContact = $request->getSchemeAndHttpHost() . $this->generateUrl('app_contact');
        $urlProfil = $request->getSchemeAndHttpHost() . $this->generateUrl('app_profil');

        $this->bus->dispatch(
            new MailUpdateUserByAdmin(
                $user->getEmail(),
                $user->getName(),
                $user->getFirstname(),
                $urlContact,
                $urlProfil
            )
        );

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $datetimeNormalizer = new DateTimeNormalizer([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s']); // Format de date personnalisé
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$datetimeNormalizer, $normalizer]);

        $data = $serializer->normalize($user, null, ['groups' => 'admin_manage_user']);

        return new JsonResponse($data, Response::HTTP_OK, []);
    }

    #[Deprecated]
    #[Route('/api/admin/manager/user/{id}/edit/role',
        name: 'app_admin_manage_role_user_edit',
        methods: [
            'GET',
            'POST',
            'UPDATE'
        ]
    )]
    public function editRoleUser(
        Request $request,
        User $user
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $date = new DateTimeImmutable('now');
        $message = [
            'id' => $user->getId(),
            'updatedAt' => $date->format('d/m/Y')
        ];
        if ($request->request->get('role')) {
            $userRole = $user->getRoles();
            $authorization = true;

            foreach ($userRole as $role) {
                if ($role === "ROLE_SUPER_ADMIN") {
                    $authorization = false;
                    break;
                }
            }
            if (!$authorization) {
                $message = "assign role forbidden, impossible to change role SUPER ADMIN";
                return new JsonResponse($message, Response::HTTP_FORBIDDEN, []);
            } else {
                $role = ['ROLE_USER', $request->request->get('role')];
                $user->setRoles($role);
            }
            $this->userRepository->add($user, true);
            $listRole = '';
            foreach ($user->getRoles() as $role) {
                $listRole .= $role . ' - ';
            }
            $message += [
                'roles' => $listRole
            ];
        }

        $user->setUpdatedAt($date);
        $this->userRepository->add($user, false);

        return new JsonResponse($message, Response::HTTP_OK, []);
    }

    #[Route('/api/admin/manage/user/{id}/delete', name: 'app_admin_delete_user', methods: ['DELETE'])]
    public function deleteUser(User $user, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $this->userDeletionService->deleteUser($user);

        return new JsonResponse(['id' => $user->getId()], Response::HTTP_OK);
    }
}
