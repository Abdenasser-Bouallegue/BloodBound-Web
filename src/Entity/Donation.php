<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use App\Repository\DonationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DonationRepository::class)]
class Donation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Emergency::class, inversedBy: 'donations')]
    #[JoinColumn(name: 'emergency_id', referencedColumnName: 'id', onDelete:"CASCADE")]
    private Emergency|null $emergency = null;
    
    #[OneToOne(targetEntity: Appointment::class, inversedBy: 'donation', cascade: ['persist', 'remove'])]
    #[JoinColumn(name: 'appointment_id', referencedColumnName: 'id')]
    private ?Appointment $appointment = null;
    

    public function getId(): ?int
    {
        return $this->id;
    }  

    public function getEmergency(): ?Emergency
    {
        return $this->emergency;
    }

    public function setEmergency(?Emergency $emergency): self
    {
        $this->emergency = $emergency;

        return $this;
    }

    public function getAppointment(): ?Appointment
    {
        return $this->appointment;
    }

    public function setAppointment(Appointment $appointment): self
    {
        $this->appointment = $appointment;

        return $this;
    }

}