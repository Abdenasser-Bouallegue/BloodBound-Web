<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    const ROLE_ADMIN = 'ROLE_ADMIN';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    //#[Groups("users")]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups("users")]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    //#[Groups("users")]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Please enter a name')]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'The name must be at least {{ limit }} characters',
        maxMessage: 'The name cannot exceed {{ limit }} characters'
    )]
    #[Groups("users")]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Please enter a valid phone number')]
    #[Assert\Length(
        min: 8,
        max: 12,
        minMessage: 'The phone number must be at least {{ limit }} digits',
        maxMessage: 'The phone number cannot exceed {{ limit }} digits'
    )]
    #[Groups("users")]
    private ?string $number = null;

    #[ORM\Column]
    #[Assert\GreaterThan(20)]
    //#[Groups("users")]
    private ?int $age = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'The location address be at least {{ limit }} characters',
        maxMessage: 'The location address cannot exceed {{ limit }} characters'
    )]
    // #[Groups("users")]
    private ?string $location = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Choice(choices: ['Ready', 'Waiting'], message: 'Invalid Status')]
    // #[Groups("users")]
    private ?string $donationStatus = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'], message: 'Invalid type of blood ')]
    // #[Groups("users")]
    private ?string $bloodType = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Event::class)]
    private Collection $events;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ProductCategory::class)]
    private Collection $productCategories;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Emergency::class)]
    private Collection $emergencies;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ArticleCategory::class)]
    private Collection $articleCategories;

    #[ORM\Column]
    //#[Groups("users")]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    //#[Groups("users")]
    private ?\DateTimeImmutable $updatedAt = null;

    #[OneToOne(targetEntity: Basket::class, mappedBy: 'user')]
    private Basket|null $basket = null;

    #[ORM\Column(type: 'boolean')]
    //#[Groups("users")]
    private $isVerified = false;

    #[ORM\Column(length: 255)]
    // #[Groups("users")]
    private ?string $userRole = null;

    #[ORM\Column(nullable: true)]
    //#[Groups("users")]
    private ?bool $isArchived = null;

    #[ORM\Column]
    //#[Groups("users")]
    private ?bool $isBanned = null;

    #[ORM\Column]
    //#[Groups("users")]
    private ?float $points = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Donation::class)]
    private Collection $donations;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Appointment::class)]
    private Collection $appointments;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->events = new ArrayCollection();
        $this->productCategories = new ArrayCollection();
        $this->emergencies = new ArrayCollection();
        $this->articleCategories = new ArrayCollection();
        $this->isBanned = false;
        $this->isArchived = false;
        $this->roles = ['ROLE_USER'];
        // $this->points = 100000;
        $this->donations = new ArrayCollection();
        $this->appointments = new ArrayCollection();
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
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
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

    public function isAdmin(): bool
    {
        return in_array(self::ROLE_ADMIN, $this->getRoles());
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
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
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

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getDonationStatus(): ?string
    {
        return $this->donationStatus;
    }

    public function setDonationStatus(?string $donationStatus): self
    {
        $this->donationStatus = $donationStatus;

        return $this;
    }

    public function getBloodType(): ?string
    {
        return $this->bloodType;
    }

    public function setBloodType(string $bloodType): self
    {
        $this->bloodType = $bloodType;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setUser($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getUser() === $this) {
                $event->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProductCategory>
     */
    public function getProductCategories(): Collection
    {
        return $this->productCategories;
    }

    public function addProductCategory(ProductCategory $productCategory): self
    {
        if (!$this->productCategories->contains($productCategory)) {
            $this->productCategories->add($productCategory);
            $productCategory->setUser($this);
        }

        return $this;
    }

    public function removeProductCategory(ProductCategory $productCategory): self
    {
        if ($this->productCategories->removeElement($productCategory)) {
            // set the owning side to null (unless already changed)
            if ($productCategory->getUser() === $this) {
                $productCategory->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Emergency>
     */
    public function getEmergencies(): Collection
    {
        return $this->emergencies;
    }

    public function addEmergency(Emergency $emergency): self
    {
        if (!$this->emergencies->contains($emergency)) {
            $this->emergencies->add($emergency);
            $emergency->setUser($this);
        }

        return $this;
    }

    public function removeEmergency(Emergency $emergency): self
    {
        if ($this->emergencies->removeElement($emergency)) {
            // set the owning side to null (unless already changed)
            if ($emergency->getUser() === $this) {
                $emergency->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ArticleCategory>
     */
    public function getArticleCategories(): Collection
    {
        return $this->articleCategories;
    }

    public function addArticleCategory(ArticleCategory $articleCategory): self
    {
        if (!$this->articleCategories->contains($articleCategory)) {
            $this->articleCategories->add($articleCategory);
            $articleCategory->setUser($this);
        }

        return $this;
    }

    public function removeArticleCategory(ArticleCategory $articleCategory): self
    {
        if ($this->articleCategories->removeElement($articleCategory)) {
            // set the owning side to null (unless already changed)
            if ($articleCategory->getUser() === $this) {
                $articleCategory->setUser(null);
            }
        }

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

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getUserRole(): ?string
    {
        return $this->userRole;
    }

    public function setUserRole(string $userRole): self
    {
        $this->userRole = $userRole;

        return $this;
    }

    public function getCart(): ?Basket
    {
        return $this->basket;
    }

    public function setCart(?Basket $cart): self
    {
        $this->basket = $cart;

        return $this;
    }

    public function IsArchived(): ?bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(?bool $isArchived): self
    {
        $this->isArchived = $isArchived;

        return $this;
    }

    public function IsBanned(): ?bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(bool $isBanned): self
    {
        $this->isBanned = $isBanned;

        return $this;
    }

    public function getPoints(): ?float
    {
        return $this->points;
    }

    public function setPoints(float $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Donation>
     */
    public function getDonations(): Collection
    {
        return $this->donations;
    }

    public function addDonation(Donation $donation): self
    {
        if (!$this->donations->contains($donation)) {
            $this->donations->add($donation);
            $donation->setUser($this);
        }

        return $this;
    }

    public function removeDonation(Donation $donation): self
    {
        if ($this->donations->removeElement($donation)) {
            // set the owning side to null (unless already changed)
            if ($donation->getUser() === $this) {
                $donation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Appointment>
     */
    public function getAppointments(): Collection
    {
        return $this->appointments;
    }

    public function addAppointment(Appointment $appointment): self
    {
        if (!$this->appointments->contains($appointment)) {
            $this->appointments->add($appointment);
            $appointment->setUser($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): self
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getUser() === $this) {
                $appointment->setUser(null);
            }
        }

        return $this;
    }
}
