<?php

namespace App\Entity;

use App\Repository\HotelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HotelRepository::class)]
class Hotel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idH = null;

    #[ORM\Column(length: 255)]
    private ?string $Nom_hotel = null;

    #[ORM\Column]
    private ?int $Nbre_etoile = null;

    #[ORM\Column(length: 255)]
    private ?string $Adresse_hotel = null;

    #[ORM\Column]
    private ?int $prix_nuit = null;

    #[ORM\OneToMany(mappedBy: 'idH', targetEntity: Reservation::class)]
    private Collection $reservations;

    #[ORM\Column(length: 255, nullable: true)]
private ?string $image = null;
   /* public function __toString()
    {
        return (string)$this->getIdH();
    }*/
    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getIdH(): ?int
    {
        return $this->idH;
    }

    public function setIdh(int $idH): static
    {
        $this->idH = $idH;

        return $this;
    }

    public function getNomHotel(): ?string
    {
        return $this->Nom_hotel;
    }

    public function setNomHotel(string $Nom_hotel): static
    {
        $this->Nom_hotel = $Nom_hotel;

        return $this;
    }

    public function getNbreEtoile(): ?int
    {
        return $this->Nbre_etoile;
    }

    public function setNbreEtoile(int $Nbre_etoile): static
    {
        $this->Nbre_etoile = $Nbre_etoile;

        return $this;
    }

    public function getAdresseHotel(): ?string
    {
        return $this->Adresse_hotel;
    }

    public function setAdresseHotel(string $Adresse_hotel): static
    {
        $this->Adresse_hotel = $Adresse_hotel;

        return $this;
    }

    public function getPrixNuit(): ?int
    {
        return $this->prix_nuit;
    }

    public function setPrixNuit(int $prix_nuit): static
    {
        $this->prix_nuit = $prix_nuit;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setIdH($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getIdH() === $this) {
                $reservation->setIdH(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return  $this->Nom_hotel. ' ' . $this->Nbre_etoile. ' '.$this->Adresse_hotel .' ('.$this->prix_nuit.' )';
    }

    public function getImage(): ?string
    {
        return $this->image;
    }
    public function setImage(?string $image): static
    {
        if ($image !== null) {
            $this->image = $image;
        }
    
        return $this;
    }
    
   
}
