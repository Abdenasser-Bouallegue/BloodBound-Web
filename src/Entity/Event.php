<?php

namespace App\Entity;
use App\Entity\Ticket;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToOne;
use App\Repository\EventRepository;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
//controle saisie de titre
    #[ORM\Column(length: 30)]
    #[assert\NotBlank(message: 'is null,Please enter an event title.')]
    #[Assert\Length(
        min: 5,
        max: 30,
        minMessage: 'The title must be at least {{ limit }} characters',
        maxMessage: 'The title cannot exceed {{ limit }} characters'
    )]
    private ?string $title = null;
//controle saisie de description
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Please enter an event description.')]
    #[Assert\Length(
        min: 20,
        max: 255,
        minMessage: 'The description must be at least {{ limit }} characters',
        maxMessage: 'The description cannot exceed {{ limit }} characters'
    )]
    private ?string $description = null;
//controle saisie de statut
    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: ['complet', 'non complet'], message: 'Invalid event status.')]
    private ?string $status = null;
    
//contrôle saisie de Location
    #[ORM\Column(length: 40)] //NotBlank !!!! un seul champs
    #[Assert\NotBlank(message: 'Please enter a location.')]
    #[Assert\Length(
        min: 5,
        max: 40,
        minMessage: 'Location description must be at least {{ limit }} characters',
        maxMessage: 'Location description cannot exceed {{ limit }} characters'
    )]
    private ?string $location = null;
//contrôle saisie de image
    #[ORM\Column(length: 255)] //NotBlank !!!!
    //#[Assert\NotBlank(message: 'Please enter an image.')]
    private ?string $image = null;
//contrôle saisie de date event 
#[ORM\Column(type: Types::DATE_MUTABLE)]

    #[Assert\GreaterThan('today')]  
    private ?\DateTimeInterface $eventDate = null;
//contrôle saisie de debut heure
    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotBlank(message: 'Please enter start time.')]
    private ?\DateTimeInterface $startTime = null;
//contrôle saisie de fin heure
    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotBlank(message: 'Please enter end time.')]
    private ?\DateTimeInterface $endTime = null;

    
    
    #[ManyToOne(targetEntity: User::class, inversedBy: 'events')]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete:"CASCADE")]
    private User|null $user = null;

    #[OneToOne(targetEntity: Ticket::class, mappedBy: 'event', cascade:["persist","remove"])]
    private ?Ticket $ticket = null;

    
    public function __construct()
    {
        $this->eventDate = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getEventDate(): ?\DateTimeInterface
    {
        return $this->eventDate;
    }

    public function setEventDate(\DateTimeInterface $eventDate): self
    {
        $this->eventDate = $eventDate;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(?Ticket $ticket): self
    {
        $this->ticket = $ticket;

        return $this;
    }

    
   // /**
   //  * @return Collection<int, Ticket>
   //  */
   // public function getTickets(): Collection
   // {
    //    return $this->tickets;
   // }
 
}   