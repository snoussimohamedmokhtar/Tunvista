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

    #[ORM\Column]
    private ?int $nbreChambre = null;

    #[ORM\Column(length: 255)]
    private ?string $typeChambre = null;

    #[ORM\Column]
    private ?int $nbreAdulte = null;

    #[ORM\Column]
    private ?int $nbreEnfant = null;

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

    public function getNbreChambre(): ?int
    {
        return $this->nbreChambre;
    }

    public function setNbreChambre(int $nbreChambre): static
    {
        $this->nbreChambre = $nbreChambre;

        return $this;
    }

    public function getTypeChambre(): ?string
    {
        return $this->typeChambre;
    }

    public function setTypeChambre(string $typeChambre): static
    {
        $this->typeChambre = $typeChambre;

        return $this;
    }

    public function getNbreAdulte(): ?int
    {
        return $this->nbreAdulte;
    }

    public function setNbreAdulte(int $nbreAdulte): static
    {
        $this->nbreAdulte = $nbreAdulte;

        return $this;
    }

    public function getNbreEnfant(): ?int
    {
        return $this->nbreEnfant;
    }

    public function setNbreEnfant(int $nbreEnfant): static
    {
        $this->nbreEnfant = $nbreEnfant;

        return $this;
    }
    public function calculateTotalPrice(): ?float
    {
        // Vérifier si toutes les informations nécessaires sont présentes
        if ($this->getDateArrivee() && $this->getDateDepart() && $this->getIdH() && $this->getNbreChambre() && $this->getTypeChambre()) {
            // Calcul du nombre total de nuits
            $totalNights = $this->getDateArrivee()->diff($this->getDateDepart())->days;

            // Récupérer le prix par nuit de l'hôtel
            $prixParNuit = $this->getIdH()->getPrixNuit();

            // Calcul du prix de base avant ajustements
            $prixBase = $totalNights * $prixParNuit;

            // Ajouter des ajustements supplémentaires en fonction d'autres attributs
            // Par exemple, ajouter des frais supplémentaires pour le type de chambre, le nombre d'adultes, le nombre d'enfants, etc.
            $ajustementsSupplementaires = 0;
            // Ajoutez vos ajustements ici...

            // Calcul du prix total en ajoutant le prix de base et tous les ajustements supplémentaires
            $prixTotal = $prixBase + $ajustementsSupplementaires;

            // Retournez le prix total calculé
            return $prixTotal;
        }

        // Retournez null si certaines informations nécessaires sont manquantes pour calculer le prix total
        return null;
    }

    

}
