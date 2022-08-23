<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $firstname;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $birthday;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $postalCode;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $city;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $address;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $memberStatus;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $memberSeniority;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $associationRegistrationDate;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $updatedAt;

    #[ORM\Column(type: 'boolean')]
    private $isAssociationMember;

    #[ORM\OneToMany(mappedBy: 'gameMaster', targetEntity: Game::class)]
    private $games;

    #[ORM\ManyToMany(targetEntity: Game::class, mappedBy: 'players')]
    private $playersGames;

    #[ORM\ManyToMany(targetEntity: StatusUserInGame::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private $statusUserInGames;

    #[ORM\OneToOne(inversedBy: 'user', targetEntity: ProfilPicture::class, cascade: ['persist', 'remove'])]
    private $profilPicture;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->games = new ArrayCollection();
        $this->playersGames = new ArrayCollection();
        $this->statusUserInGames = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable('now');
        $this->isAssociationMember = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getMemberStatus(): ?string
    {
        return $this->memberStatus;
    }

    public function setMemberStatus(string $memberStatus): self
    {
        $this->memberStatus = $memberStatus;

        return $this;
    }

    public function getMemberSeniority(): ?string
    {
        return $this->memberSeniority;
    }

    public function setMemberSeniority(string $memberSeniority): self
    {
        $this->memberSeniority = $memberSeniority;

        return $this;
    }

    public function getAssociationRegistrationDate(): ?\DateTimeInterface
    {
        return $this->associationRegistrationDate;
    }

    public function setAssociationRegistrationDate(?\DateTimeInterface $associationRegistrationDate): self
    {
        $this->associationRegistrationDate = $associationRegistrationDate;

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

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function isIsAssociationMember(): ?bool
    {
        return $this->isAssociationMember;
    }

    public function setIsAssociationMember(bool $isAssociationMember): self
    {
        $this->isAssociationMember = $isAssociationMember;

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setGameMaster($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getGameMaster() === $this) {
                $game->setGameMaster(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getPlayersGames(): Collection
    {
        return $this->playersGames;
    }

    public function addPlayersGame(Game $playersGame): self
    {
        if (!$this->playersGames->contains($playersGame)) {
            $this->playersGames[] = $playersGame;
            $playersGame->addPlayer($this);
        }

        return $this;
    }

    public function removePlayersGame(Game $playersGame): self
    {
        if ($this->playersGames->removeElement($playersGame)) {
            $playersGame->removePlayer($this);
        }

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
            $statusUserInGame->addUser($this);
        }

        return $this;
    }

    public function removeStatusUserInGame(StatusUserInGame $statusUserInGame): self
    {
        if ($this->statusUserInGames->removeElement($statusUserInGame)) {
            $statusUserInGame->removeUser($this);
        }

        return $this;
    }

    public function getProfilPicture(): ?ProfilPicture
    {
        return $this->profilPicture;
    }

    public function setProfilPicture(?ProfilPicture $profilPicture): self
    {
        $this->profilPicture = $profilPicture;

        return $this;
    }
}
