<?php

namespace App\Entity;

use App\Repository\NotifaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotifaRepository::class)]
class Notifa
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $ville_ann = null;

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'notifas')]
    private Collection $users;

    #[ORM\OneToMany(targetEntity: Annonce::class, mappedBy: 'notifas')]
    private Collection $annonces; // This is the collection for the ManyToMany relationship with Annonce

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->annonces = new ArrayCollection(); // Initialize the collection for Annonce
    }

    public function getId(): ?int
    {
            return $this->id;
    }

    public function getVilleAnn(): ?\DateTimeInterface
    {
        return $this->ville_ann;
    }

    public function setVilleAnn(\DateTimeInterface $ville_ann): static
    {
        $this->ville_ann = $ville_ann;
        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUsers(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addNotifa($this);
        }
        return $this;
    }

    public function removeUsers(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeNotifa($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, Annonce>
     */
    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    public function addAnnonces(Annonce $annonces): static
    {
        if (!$this->annonces->contains($annonces)) {
            $this->annonces->add($annonces);
            $annonces->addNotifa($this);
        }
        return $this;
    }

    public function removeAnnonces(Annonce $annonce): static
    {
        if ($this->annonces->removeElement($annonce)) {
            $annonce->removeNotifa($this);
        }
        return $this;
    }
}
