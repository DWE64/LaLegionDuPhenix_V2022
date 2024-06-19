<?php

namespace App\Controller;

use App\Service\FileUploader;
use App\Service\QrCodeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;
class QrCodeController extends AbstractController
{
    private QrCodeService $qrCodeService;
    private FileUploader $fileUploader;

    public function __construct(QrCodeService $qrCodeService, FileUploader $fileUploader)
    {
        $this->qrCodeService = $qrCodeService;
        $this->fileUploader = $fileUploader;
    }

    #[Route('/generate-qrcode/{userId}', name: 'generate_qrcode')]
    public function generateQrCode(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $gamesData = [];

        foreach ($user->getPlayersGames() as $game) {
            $gamesData[] = [
                'title' => $game->getTitle(),
                'weekSlot' => $game->getWeekSlots(),
                'halfDaySlot' => $game->getHalfDaySlots(),
                'isPresent' => $this->checkUserPresence($user, $game),
                'role' => $this->getUserRole($user, $game)
            ];
        }

        $data = [
            'Nom' => $user->getName(),
            'Prenom' => $user->getFirstname(),
            'Pseudo' => $user->getUsername(),
            'Statut du Membre' => $user->getMemberStatus(),
            'Date d\'Inscription à l\'Asso' => $user->getAssociationRegistrationDate()->format('Y-m-d'),
            'Date de Création du Compte' => $user->getCreatedAt()->format('Y-m-d'),
            'Date de Modification du Compte' => $user->getUpdatedAt() ? $user->getUpdatedAt()->format('Y-m-d') : 'Non modifié',
            'Date de Naissance' => $user->getBirthday()->format('Y-m-d'),
            'Games' => $gamesData
        ];

        $qrCodeContent = json_encode($data);
        $qrCodePath = $this->qrCodeService->generateQrCode($qrCodeContent);

        $uploadedFileName = $this->fileUploader->uploadQrcode(new File($qrCodePath));


        return $this->render('qr_code/index.html.twig', [
            'qrCodePath' => '/pictures/qr_codes/' . $uploadedFileName,
        ]);
    }

    private function checkUserPresence($user, $game): string
    {
        foreach ($game->getStatusUserInGames() as $status) {
            if ($status->getUser()->contains($user)) {
                return $status->isIsPresent() ? 'Validé' : 'Invalidé';
            }
        }
        return 'Non spécifié';
    }

    private function getUserRole($user, $game): string
    {
        if ($game->getGameMaster() === $user) {
            return 'Master';
        } elseif ($game->getPlayers()->contains($user)) {
            return 'Player';
        }
        return 'Non spécifié';
    }
}
