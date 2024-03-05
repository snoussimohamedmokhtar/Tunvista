<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_evenement = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_deb = null;


    #[ORM\Column(length: 255)]
    private ?string $description_e = null;

    #[ORM\Column(length: 255)]
    private ?string $titre_e = null;

    #[ORM\Column(length: 255)]
    private ?string $ville_e = null;

    #[ORM\Column(length: 255)]
    private ?string $maps_even = null;

    public function getIdEvenement(): ?int
    {
        return $this->id_evenement;
    }

    public function setIdEvenement(int $id_evenement): static
    {
        $this->id_evenement = $id_evenement;

        return $this;
    }

    public function getDateDeb(): ?\DateTimeInterface
    {
        return $this->date_deb;
    }

    public function setDateDeb(\DateTimeInterface $date_deb): static
    {
        $this->date_deb = $date_deb;

        return $this;
    }


    public function getDescriptionE(): ?string
    {
        return $this->description_e;
    }

    public function setTypeE(string $description_e): static
    {
        $this->description_e = $description_e;

        return $this;
    }

    public function getTitreE(): ?string
    {
        return $this->titre_e;
    }

    public function setTitreE(string $titre_e): static
    {
        $this->titre_e = $titre_e;

        return $this;
    }

    public function getVilleE(): ?string
    {
        return $this->ville_e;
    }

    public function setVilleE(string $ville_e): static
    {
        $this->ville_e = $ville_e;

        return $this;
    }

    public function getMapsEven(): ?string
    {
        return $this->maps_even;
    }

    public function setMapsEven(string $maps_even): static
    {
        $this->maps_even = $maps_even;

        return $this;
    }
}
