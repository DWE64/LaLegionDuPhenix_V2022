<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\GameRepository;
use App\Repository\StatusUserInGameRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use App\Service\QrCodeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QrCodeController extends AbstractController
{
    private QrCodeService $qrCodeService;
    private UserRepository $userRepo;

    public function __construct(
        QrCodeService $qrCodeService,
        UserRepository $userRepo
    )
    {
        $this->qrCodeService = $qrCodeService;
        $this->userRepo = $userRepo;
    }

    /**
     * @Route("/generate-qrcode/{userId}", name="generate_qrcode")
     */
    public function generateQrCode(int $userId): Response
    {
        // Récupérer les informations utilisateur
        $user = $this->userRepo->find($userId);
        if (!($user instanceof User)) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // Convertir les informations utilisateur en chaîne de caractères
        $data = [
            'Nom' => $user->getName(),
            'Prenom' => $user->getFirstname(),
            'Pseudo' => $user->getUsername(),
            'Statut du Membre' => $user->getMemberStatus(),
            'Date d\'Inscription à l\'Asso' => $user->getAssociationRegistrationDate()->format('Y-m-d'),
            'Date de Création du Compte' => $user->getCreatedAt()->format('Y-m-d'),
            'Date de Modification du Compte' => $user->getUpdatedAt() ? $user->getUpdatedAt()->format('Y-m-d') : 'Non modifié',
            'Date de Naissance' => $user->getBirthday()->format('Y-m-d')
        ];
        $dataString = json_encode($data);

        // Générer le QR code
        $qrCodePath = $this->qrCodeService->generateQrCode($dataString);

        // Afficher l'image du QR code
        return $this->render('qrcode/display.html.twig', [
            'qrCodePath' => $qrCodePath
        ]);
    }
}
