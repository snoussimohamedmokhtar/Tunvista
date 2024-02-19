<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idR = null;


    #[ORM\Column]
    private ?int $Id_client = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $Date_arrivee = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $Date_depart = null;

    #[ORM\Column(length: 255)]
    private ?string $TypeR = null;

    #[ORM\Column]
    private ?int $prix_total = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'idH', referencedColumnName: 'id_h', nullable: false)]
    private ?Hotel $idH = null;

    public function getIdR(): ?int
    {
        return $this->idR;
    }
    public function setIdR(?int $idR): void
    {
        $this->idR = $idR;
    }

    public function getIdClient(): ?int
    {
        return $this->Id_client;
    }

    public function setIdClient(int $Id_client): static
    {
        $this->Id_client = $Id_client;

        return $this;
    }

    public function getDateArrivee(): ?\DateTimeInterface
    {
        return $this->Date_arrivee;
    }

    public function setDateArrivee(\DateTimeInterface $Date_arrivee): static
    {
        $this->Date_arrivee = $Date_arrivee;

        return $this;
    }

    public function getDateDepart(): ?\DateTimeInterface
    {
        return $this->Date_depart;
    }

    public function setDateDepart(\DateTimeInterface $Date_depart): static
    {
        $this->Date_depart = $Date_depart;

        return $this;
    }

    public function getTypeR(): ?string
    {
        return $this->TypeR;
    }

    public function setTypeR(string $TypeR): static
    {
        $this->TypeR = $TypeR;

        return $this;
    }

    public function getPrixTotal(): ?int
    {
        return $this->prix_total;
    }

    public function setPrixTotal(float $prix_total): static
    {
        $this->prix_total = $prix_total;

        return $this;
    }

    public function getIdH(): ?Hotel
    {
        return $this->idH;
    }

    public function setIdH(?Hotel $idH): static
    {
        $this->idH = $idH;

        return $this;
    }

}
