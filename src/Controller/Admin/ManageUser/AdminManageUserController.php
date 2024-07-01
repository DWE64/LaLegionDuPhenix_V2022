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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\SerializerInterface;


#[IsGranted('ROLE_STAFF')]
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
    public function index(): Response
    {
        $user = $this->getUser();
        $roles = $user->getRoles();
        $roleUser = 'ROLE_USER';

        if (in_array('ROLE_SUPER_ADMIN', $roles)) {
            $roleUser = 'ROLE_SUPER_ADMIN';
        } if (in_array('ROLE_STAFF', $roles)) {
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

        $users = $this->userRepository->findAll();
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

    #[Route('/admin/manager/user/{id}/change_status_is_association_member',
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
        if ($request->isXmlHttpRequest()) {
            $date = new DateTimeImmutable('now');

            if ($user->isIsAssociationMember()) {
                $user->setIsAssociationMember(false);
                $user->setMemberStatus(StatusService::MEMBER_NOT_REGISTER);
                $user->setAssociationRegistrationDate(null);
                $message = [
                    'id' => $user->getId(),
                    'status' => StatusService::MEMBER_INACTIVE,
                    'updatedAt' => $date->format('d/m/Y'),
                    'associationRegistrationDate' => '-',
                    'memberStatus' => StatusService::MEMBER_NOT_REGISTER
                ];
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
                $message = [
                    'id' => $user->getId(),
                    'status' => StatusService::MEMBER_ACTIVE,
                    'updatedAt' => $date->format('d/m/Y'),
                    'associationRegistrationDate' => $registrationDate->format('d/m/Y'),
                    'memberStatus' => StatusService::MEMBER_REGISTER
                ];
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

            return new JsonResponse($message, Response::HTTP_OK, []);
        } else {
            $message = Response::HTTP_NOT_MODIFIED;
            return new JsonResponse($message, Response::HTTP_NOT_MODIFIED, []);
        }
    }

    #[Route('/admin/manager/user/{id}/checked_oldest',
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
        if ($request->isXmlHttpRequest()) {
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
        } else {
            $message = Response::HTTP_NOT_MODIFIED;
            return new JsonResponse($message, Response::HTTP_NOT_MODIFIED, []);
        }
    }

    #[Route('/admin/manager/user/{id}/edit',
        name: 'app_admin_manage_user_edit',
        methods: [
            'GET',
            'POST',
            'UPDATE'
        ]
    )]
    public function editUser(
        Request $request,
        User $user
    ): JsonResponse {
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
            if ($request->request->get('seniority')) {
                $status = ($request->request->get(
                        'seniority'
                    ) === StatusService::MEMBER_NEW) ? StatusService::MEMBER_NEW : StatusService::MEMBER_OLD;
                $user->setMemberSeniority($status);
                $message += [
                    'member-seniority' => $user->getMemberSeniority()
                ];
            }

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
            return new JsonResponse($message, Response::HTTP_OK, []);
        } else {
            $message = Response::HTTP_NOT_MODIFIED;
            return new JsonResponse($message, Response::HTTP_NOT_MODIFIED, []);
        }
    }

    #[Route('/admin/manager/user/{id}/edit/role',
        name: 'app_admin_manage_role_user_edit',
        methods: [
            'GET',
            'POST',
            'UPDATE'
        ]
    )]
    #[IsGranted('ROLE_SUPER_ADMIN')]
    public function editRoleUser(
        Request $request,
        User $user
    ): JsonResponse {
        if ($request->isXmlHttpRequest()) {
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
        } else {
            $message = Response::HTTP_NOT_MODIFIED;
            return new JsonResponse($message, Response::HTTP_NOT_MODIFIED, []);
        }
    }

    #[Route('/api/admin/manage/user/{id}/delete', name: 'app_admin_delete_user', methods: ['POST'])]
    public function deleteUser(User $user, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $this->userDeletionService->deleteUser($user);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['id' => $user->getId()], Response::HTTP_OK);
        } else {
            return new JsonResponse(['id' => $user->getId()], Response::HTTP_NOT_MODIFIED, []);
        }
    }
}
