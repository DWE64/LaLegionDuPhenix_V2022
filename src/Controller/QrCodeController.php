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

        $memberCategory = $this->determineMemberCategory($user->getBirthday());

        $gamesData = [];

        foreach ($user->getPlayersGames() as $game) {
            $gamesData[] = $this->formatGameData($user, $game, 'Player');
        }

        foreach ($user->getGames() as $game) {
            $gamesData[] = $this->formatGameData($user, $game, 'Master');
        }

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

        $gamesData = [];

        foreach ($user->getPlayersGames() as $game) {
            $gamesData[] = $this->formatGameData($user, $game, 'Player');
        }

        foreach ($user->getGames() as $game) {
            $gamesData[] = $this->formatGameData($user, $game, 'Master');
        }

        return $this->render('qrcode/view.html.twig', [
            'name' => $user->getName(),
            'firstname' => $user->getFirstname(),
            'username' => $user->getUsername(),
            'memberCategory' => $memberCategory,
            'registrationDate' => $user->getAssociationRegistrationDate()->format('d-m-y'),
            'updatedAt' => $user->getUpdatedAt() ? $user->getUpdatedAt()->format('d-m-y') : 'Non modifié',
            'birthday' => $user->getBirthday()->format('d-m-y'),
            'gamesData' => $gamesData,
        ]);
    }

    private function formatGameData($user, $game, $role): array
    {

        $presence = 'Présence non renseignée';
        foreach ($game->getStatusUserInGames() as $status) {
            if ($status->getUser()->contains($user)) {
                $presence = $status->isIsPresent() ? 'Présent' : 'Absent';
            }
        }

        $weekSlot = $this->determineWeekSlot($game);
        $halfDaySlot = $this->determineHalfDaySlot($game);

        return [
            'Titre' => $game->getTitle(),
            'Creneau' => $weekSlot. ' - '.$halfDaySlot,
            'Presence'=>$presence,
            'Role'=>$role,
        ];
    }

    private function determineMemberCategory(\DateTimeInterface $birthday): string
    {
        $today = new \DateTime();
        $age = $today->diff($birthday)->y;

        if ($age < 15) {
            return 'Membre enfant / mineur';
        } elseif ($age <= 18) {
            return 'Membre mineur / jeune adulte';
        } else {
            return 'Membre majeur';
        }
    }

    private function determineWeekSlot(Game $game): string
    {
        if ($game->getWeekSlots() === "CRENEAU_1") {
            return 'Semaine pair';
        } else {
            return 'Semaine impair';
        }
    }

    private function determineHalfDaySlot(Game $game): string
    {
        if ($game->getHalfDaySlots() === "APREM") {
            return 'Après midi';
        } else {
            return 'Soir';
        }
    }
}
