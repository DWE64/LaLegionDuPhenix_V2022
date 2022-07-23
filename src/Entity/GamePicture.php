<?php

namespace App\Entity;

use App\Repository\GamePictureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GamePictureRepository::class)]
class GamePicture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $gamePicture;

    #[ORM\OneToOne(mappedBy: 'picture', targetEntity: Game::class, cascade: ['persist', 'remove'])]
    private $game;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGamePicture(): ?string
    {
        return $this->gamePicture;
    }

    public function setGamePicture(?string $gamePicture): self
    {
        $this->gamePicture = $gamePicture;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        // unset the owning side of the relation if necessary
        if ($game === null && $this->game !== null) {
            $this->game->setPicture(null);
        }

        // set the owning side of the relation if necessary
        if ($game !== null && $game->getPicture() !== $this) {
            $game->setPicture($this);
        }

        $this->game = $game;

        return $this;
    }
}
