<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Message\MailRegistration;
use App\Repository\UserRepository;
use App\Security\AppCustomAuthenticator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    private TranslatorInterface $translator;
    private UserRepository $repo_user;
    private MessageBusInterface $bus;

    public function __construct(TranslatorInterface $translator, UserRepository $repo_user, MessageBusInterface $bus)
    {
        $this->translator = $translator;
        $this->repo_user = $repo_user;
        $this->bus = $bus;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppCustomAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setName($form->get('name')->getData());
            $user->setFirstname($form->get('firstname')->getData());
            $user->setUsername($form->get('username')->getData());
            $birthday=new DateTime($form->get('birthday')->getData());
            $user->setBirthday($birthday);
            $user->setAddress($form->get('address')->getData());
            $user->setPostalCode($form->get('postalCode')->getData());
            $user->setCity($form->get('city')->getData());
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $roles[] = "ROLE_USER";
            $user->setEmail($form->get('email')->getData());
            $user->setRoles($roles);

            $this->repo_user->add($user,true);

            $urlProfil=$request->getSchemeAndHttpHost().$this->generateUrl('app_profil');
            $this->bus->dispatch(
                new MailRegistration($user->getEmail(), $user->getName(), $user->getFirstname(), $urlProfil)
            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'title' => $this->translator->trans('page.registration')
        ]);
    }
}
