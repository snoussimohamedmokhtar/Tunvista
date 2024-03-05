<?php

namespace App\Entity;

use App\Repository\AnnonceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnonceRepository::class)]
class Annonce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_annonce = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(length: 255)]
    #[Assert\Notblank(message:' should not be empty')]
    private ?string $description_a = null;

    #[ORM\ManyToOne(inversedBy: 'annonces')]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $titre_a = null;

    #[ORM\Column(length: 255)]
    private ?string $ville_a = null;

    #[ORM\Column(length: 255)]
    private ?string $mapsLink = null;

    #[ORM\OneToMany(targetEntity: Notifa::class, mappedBy: 'annonces')]
    private Collection $notifas;


    public function __construct()
    {
        $this->notifas = new ArrayCollection();
    }

    /*public function __construct()
    {
        $this->users = new ArrayCollection();
    }*/
    public function getIdAnnonce(): ?int
    {
        return $this->id_annonce;
    }

    public function setIdAnnonce(int $id_annonce): static
    {
        $this->id_annonce = $id_annonce;

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

    public function getDescriptionA(): ?string
    {
        return $this->description_a;
    }

    public function setDescriptionA(string $description_a): static
    {
        $this->description_a = $description_a;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
    /*public function __toString() {
        return $this->date_debut->format('Y-m-d H:i:s') . ' ' . $this->type . ' ' . $this->user;
    }*/

    public function getTitreA(): ?string
    {
        return $this->titre_a;
    }

    public function setTitreA(string $titre_a): static
    {
        $this->titre_a = $titre_a;

        return $this;
    }

    public function getVilleA(): ?string
    {
        return $this->ville_a;
    }

    public function setVilleA(string $ville_a): static
    {
        $this->ville_a = $ville_a;

        return $this;
    }

    public function getMapsLink(): ?string
    {
        return $this->mapsLink;
    }

    public function setMapsLink(string $mapsLink): static
    {
        $this->mapsLink = $mapsLink;

        return $this;
    }

    /**
     * @return Collection<int, Notifa>
     */
    public function getNotifas(): Collection
    {
        return $this->notifas;
    }

    public function addNotifa(Notifa $notifa): static
    {
        if (!$this->notifas->contains($notifa)) {
            $this->notifas->add($notifa);
            $notifa->addIdAnnonce($this);
        }

        return $this;
    }

    public function removeNotifa(Notifa $notifa): static
    {
        if ($this->notifas->removeElement($notifa)) {
            $notifa->removeIdAnnonce($this);
        }

        return $this;
    }
}
