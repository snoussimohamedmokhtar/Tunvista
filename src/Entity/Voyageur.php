<?php

namespace App\Entity;

use App\Repository\VoyageurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: VoyageurRepository::class)]
class Voyageur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Length(max: 6)]
    private ?int $NumPass = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 10)]
    private ?string $Nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 15)]
    private ?string $Prenom = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Length(max: 2)]
    private ?int $Age = null;

    #[ORM\Column(length: 255)]
    private ?string $EtatCivil = null;

    #[ORM\ManyToOne(inversedBy: 'Voyagee')]
    private ?Voyage $voyage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumPass(): ?int
    {
        return $this->NumPass;
    }

    public function setNumPass(int $NumPass): static
    {
        $this->NumPass = $NumPass;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->Prenom;
    }

    public function setPrenom(string $Prenom): static
    {
        $this->Prenom = $Prenom;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->Age;
    }

    public function setAge(int $Age): static
    {
        $this->Age = $Age;

        return $this;
    }

    public function getEtatCivil(): ?string
    {
        return $this->EtatCivil;
    }

    public function setEtatCivil(string $EtatCivil): static
    {
        $this->EtatCivil = $EtatCivil;

        return $this;
    }

    public function getVoyage(): ?Voyage
    {
        return $this->voyage;
    }

    public function setVoyage(?Voyage $voyage): static
    {
        $this->voyage = $voyage;

        return $this;
    }
}