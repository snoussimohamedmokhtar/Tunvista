<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocationRepository::class)]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]

    #[ORM\Column]
    private ?int $id_location = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\ManyToOne(inversedBy: 'location')]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    private ?User $client = null;

    #[ORM\ManyToOne(inversedBy: 'location')]
    #[ORM\JoinColumn(name: 'voiture_id', referencedColumnName: 'id_voiture')]
    private ?Voiture $voiture = null;



    /*public function __construct()
    {
        $this->voiture = new ArrayCollection();
    }*/

    public function getIdLocation(): ?int
    {
        return $this->id_location;
    }

    public function setIdLocation(int $id_location): static
    {
        $this->id_location = $id_location;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?user $client): static
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection<int, voiture>
     */
    public function getVoiture(): ?Voiture
    {
        return $this->voiture;
    }

    public function addVoiture(voiture $voiture): static
    {
        if (!$this->voiture->contains($voiture)) {
            $this->voiture->add($voiture);
            $voiture->setLocation($this);
        }

        return $this;
    }

    public function removeVoiture(voiture $voiture): static
    {
        if ($this->voiture->removeElement($voiture)) {
            // set the owning side to null (unless already changed)
            if ($voiture->getLocation() === $this) {
                $voiture->setLocation(null);
            }
        }

        return $this;
    }

    public function setVoiture(?voiture $voiture): static
    {
        $this->voiture = $voiture;

        return $this;
    }
}
