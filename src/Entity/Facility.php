<?php

namespace App\Entity;

use App\Repository\FacilityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FacilityRepository::class)]
class Facility
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'facility', targetEntity: Appointment::class)]
    private Collection $appointments;

    #[ORM\Column]
    private ?\DateTimeImmutable $startingTime = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $endingTime = null;

    public function __construct()
    {
        $this->appointments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

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
            $appointment->setFacility($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): self
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getFacility() === $this) {
                $appointment->setFacility(null);
            }
        }

        return $this;
    }

    public function getStartingTime(): ?\DateTimeImmutable
    {
        return $this->startingTime;
    }

    public function setStartingTime(\DateTimeImmutable $startingTime): self
    {
        $this->startingTime = $startingTime;

        return $this;
    }

    public function getEndingTime(): ?\DateTimeImmutable
    {
        return $this->endingTime;
    }

    public function setEndingTime(\DateTimeImmutable $endingTime): self
    {
        $this->endingTime = $endingTime;

        return $this;
    }
}
