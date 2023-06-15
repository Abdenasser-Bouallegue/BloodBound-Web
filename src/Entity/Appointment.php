<?php

namespace App\Entity;

use FFI;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use App\Repository\AppointmentRepository;

#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
class Appointment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $rdv = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'appointments')]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete:"CASCADE")]
    private ?User $user = null;

    #[ManyToOne(targetEntity: Facility::class, inversedBy: 'appointments')]
    #[JoinColumn(name: 'facility_id', referencedColumnName: 'id', onDelete:"CASCADE")]
    private ?Facility $facility = null;

    #[OneToOne(targetEntity: Donation::class, mappedBy: 'appointment')]
    private Donation|null $donation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRdv(): ?\DateTimeImmutable
    {
        return $this->rdv;
    }

    public function setRdv(\DateTimeImmutable $date): self
    {
        $this->rdv = $date;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFacility(): ?Facility
    {
        return $this->facility;
    }

    public function setFacility(?Facility $facility): self
    {
        $this->facility = $facility;

        return $this;
    }

    public function getDonation(): ?Donation
    {
        return $this->donation;
    }

    public function setDonation(Donation $donation): self
    {
        $this->donation = $donation;

        return $this;
    }
}
