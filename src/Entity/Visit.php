<?php

namespace App\Entity;

use App\Repository\VisitRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisitRepository::class)]
class Visit
{
#[ORM\Id]  
#[ORM\GeneratedValue]                                                                                                                                                                                                                                                                                                                                                                                     
#[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateVisit = null;

    #[ORM\Column]
    private ?int $Numero = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;
    

    #[ORM\ManyToOne(inversedBy: 'visits')]
    #[ORM\JoinColumn(name: 'refB',referencedColumnName:'ref_b')]
    private ?Maison $refB = null;

    
    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(int $id): static
    {
        $this->id=$id;
        return $this;

    }
    
    public function getDateVisit(): ?\DateTimeInterface
    {
        return $this->dateVisit;
    }

    public function setDateVisit(\DateTimeInterface $dateVisit): static
    {
        $this->dateVisit = $dateVisit;

        return $this;
    }

    
    public function getNumero(): ?int
    {
        return $this->Numero;
    }

    public function setNumero(int $Numero): static
    {
        $this->Numero = $Numero;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getRefB(): ?Maison
    {
        return $this->refB;
    }

    public function setRefB(?Maison $refB): static
    {
        $this->refB = $refB;

        return $this;
    }

}
