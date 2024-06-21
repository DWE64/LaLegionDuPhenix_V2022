<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\FileUploader;
use App\Service\QrCodeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\Game;

class QrCodeController extends AbstractController
{
    private QrCodeService $qrCodeService;
    private FileUploader $fileUploader;

    public function __construct(QrCodeService $qrCodeService, FileUploader $fileUploader)
    {
        $this->qrCodeService = $qrCodeService;
        $this->fileUploader = $fileUploader;
    }

    #[Route('/generate-qrcode/{user}', name: 'generate_qrcode')]
    public function generateQrCode(User $user): Response
    {
        $viewQrCodeUrl = $this->generateUrl('view_qrcode', ['user' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        $qrCodePath = $this->qrCodeService->generateQrCode($viewQrCodeUrl, $user->getId());

        $file = new File($qrCodePath);
        $uploadedFile = new UploadedFile(
            $file->getPathname(),
            $file->getFilename(),
            $file->getMimeType(),
            UPLOAD_ERR_OK,
            true
        );

        $uploadedFileName = $this->fileUploader->uploadQrcode($uploadedFile, $user->getId());

        return $this->render('qrcode/index.html.twig', [
            'qrCodePath' => '/pictures/qr_codes/' . $uploadedFileName,
        ]);
    }

    #[Route('/view-qrcode/{user}', name: 'view_qrcode')]
    public function viewQrCode(User $user): Response
    {
        $memberCategory = $this->determineMemberCategory($user->getBirthday());

        $games = [];

        foreach ($user->getPlayersGames() as $game) {
            $games[] = $this->formatGameData($user, $game, 'Player');
        }

        foreach ($user->getGames() as $game) {
            $games[] = $this->formatGameData($user, $game, 'Master');
        }

        return $this->render('qrcode/view.html.twig', [
            'name' => $user->getName(),
            'firstname' => $user->getFirstname(),
            'username' => $user->getUsername(),
            'memberCategory' => $memberCategory,
            'registrationDate' => ($user->getAssociationRegistrationDate() !== null)? $user->getAssociationRegistrationDate()->format('d-m-Y') : 'Non inscrit',
            'updatedAt' => $user->getUpdatedAt() ? $user->getUpdatedAt()->format('d-m-Y') : 'Non modifié',
            'games' => $games,
        ]);
    }

    private function formatGameData($user, $game, $role): array
    {
        $presence = 'Présence non renseignée';
        $withBadge = null;
        foreach ($game->getStatusUserInGames() as $status) {
            if ($status->getUser()->contains($user)) {
                if($status->isIsPresent() === null){
                    $presence = 'Présence non renseignée';
                    $withBadge = null;
                }else{
                    $presence = $status->isIsPresent() ? 'Présent' : 'Absent';
                    $withBadge = $status->isIsPresent() ? true : false;
                }
            }
        }

        return [
            'id' => $game->getId(),
            'Titre' => $game->getTitle(),
            'Creneau' => $game->getWeekSlots() . ' - ' . $game->getHalfDaySlots(),
            'Presence' => $presence,
            'WithBadge' => $withBadge,
            'Role' => $role,
        ];
    }

    private function determineMemberCategory(\DateTimeInterface $birthday): string
    {
        $today = new \DateTime();
        $age = $today->diff($birthday)->y;

        if ($age < 15) {
            return 'Mineur';
        } elseif ($age <= 18) {
            return 'Jeune adulte';
        } else {
            return 'Adulte';
        }
    }
}
