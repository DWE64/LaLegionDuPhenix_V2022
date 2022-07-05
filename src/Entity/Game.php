<?php

namespace App\Entity;

use App\Repository\GameRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'games')]
    private $gameMaster;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'playersGames')]
    private $players;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $minGamePlace;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $maxGamePlace;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $assignedPlace;

    #[ORM\Column(type: 'string', length: 255)]
    private $gameStatus;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $updatedAt;

    #[ORM\ManyToMany(targetEntity: StatusUserInGame::class, mappedBy: 'games')]
    private $statusUserInGames;

    #[ORM\Column(type: 'text', nullable: true)]
    private $gameMasterCommentary;

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->statusUserInGames = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getGameMaster(): ?User
    {
        return $this->gameMaster;
    }

    public function setGameMaster(?User $gameMaster): self
    {
        $this->gameMaster = $gameMaster;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(User $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players[] = $player;
        }

        return $this;
    }

    public function removePlayer(User $player): self
    {
        $this->players->removeElement($player);

        return $this;
    }

    public function getMinGamePlace(): ?int
    {
        return $this->minGamePlace;
    }

    public function setMinGamePlace(?int $minGamePlace): self
    {
        $this->minGamePlace = $minGamePlace;

        return $this;
    }

    public function getMaxGamePlace(): ?int
    {
        return $this->maxGamePlace;
    }

    public function setMaxGamePlace(?int $maxGamePlace): self
    {
        $this->maxGamePlace = $maxGamePlace;

        return $this;
    }

    public function getAssignedPlace(): ?int
    {
        return $this->assignedPlace;
    }

    public function setAssignedPlace(?int $assignedPlace): self
    {
        $this->assignedPlace = $assignedPlace;

        return $this;
    }

    public function getGameStatus(): ?string
    {
        return $this->gameStatus;
    }

    public function setGameStatus(string $gameStatus): self
    {
        $this->gameStatus = $gameStatus;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, StatusUserInGame>
     */
    public function getStatusUserInGames(): Collection
    {
        return $this->statusUserInGames;
    }

    public function addStatusUserInGame(StatusUserInGame $statusUserInGame): self
    {
        if (!$this->statusUserInGames->contains($statusUserInGame)) {
            $this->statusUserInGames[] = $statusUserInGame;
            $statusUserInGame->addGame($this);
        }

        return $this;
    }

    public function removeStatusUserInGame(StatusUserInGame $statusUserInGame): self
    {
        if ($this->statusUserInGames->removeElement($statusUserInGame)) {
            $statusUserInGame->removeGame($this);
        }

        return $this;
    }

    public function getGameMasterCommentary(): ?string
    {
        return $this->gameMasterCommentary;
    }

    public function setGameMasterCommentary(?string $gameMasterCommentary): self
    {
        $this->gameMasterCommentary = $gameMasterCommentary;

        return $this;
    }
}
