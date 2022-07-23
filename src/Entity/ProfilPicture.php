<?php

namespace App\Entity;

use App\Repository\ProfilPictureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfilPictureRepository::class)]
class ProfilPicture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $profilPicture;

    #[ORM\OneToOne(mappedBy: 'profilPicture', targetEntity: User::class, cascade: ['persist', 'remove'])]
    private $user;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfilPicture(): ?string
    {
        return $this->profilPicture;
    }

    public function setProfilPicture(?string $profilPicture): self
    {
        $this->profilPicture = $profilPicture;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setProfilPicture(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getProfilPicture() !== $this) {
            $user->setProfilPicture($this);
        }

        $this->user = $user;

        return $this;
    }
}
