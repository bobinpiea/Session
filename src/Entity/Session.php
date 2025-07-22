<?php

namespace App\Entity;

use DatePeriod;
use DateInterval;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SessionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $placeDisponibles = null;

    #[ORM\Column]
    private ?\DateTime $dateDebut = null;

    #[ORM\Column]
    private ?\DateTime $dateFin = null;

    #[ORM\Column]
    private ?int $placeRestantes = null;

    #[ORM\Column]
    private ?int $placeReservees = null;

    /**
     * @var Collection<int, Stagiaire>
     */
    #[ORM\ManyToMany(targetEntity: Stagiaire::class, mappedBy: 'sessions')]
    private Collection $stagiaires;

    /**
     * @var Collection<int, Programme>
     */
    #[ORM\OneToMany(targetEntity: Programme::class, mappedBy: 'session', orphanRemoval: true)]
    private Collection $programmes;

    public function __construct()
    {
        $this->stagiaires = new ArrayCollection();
        $this->programmes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPlaceDisponibles(): ?int
    {
        return $this->placeDisponibles;
    }

    public function setPlaceDisponibles(int $placeDisponibles): static
    {
        $this->placeDisponibles = $placeDisponibles;

        return $this;
    }

    public function getDateDebut(): ?\DateTime
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTime $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTime $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getPlaceRestantes(): ?int
    {
        return $this->placeRestantes;
    }

    public function setPlaceRestantes(int $placeRestantes): static
    {
        $this->placeRestantes = $placeRestantes;

        return $this;
    }

    public function getPlaceReservees(): ?int
    {
        return $this->placeReservees;
    }

    public function setPlaceReservees(int $placeReservees): static
    {
        $this->placeReservees = $placeReservees;

        return $this;
    }

    /**
     * @return Collection<int, Stagiaire>
     */
    public function getStagiaires(): Collection
    {
        return $this->stagiaires;
    }

    public function addStagiaire(Stagiaire $stagiaire): static
    {
        if (!$this->stagiaires->contains($stagiaire)) {
            $this->stagiaires->add($stagiaire);
            $stagiaire->addSession($this);
        }

        return $this;
    }

    public function removeStagiaire(Stagiaire $stagiaire): static
    {
        if ($this->stagiaires->removeElement($stagiaire)) {
            $stagiaire->removeSession($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Programme>
     */
    public function getProgrammes(): Collection
    {
        return $this->programmes;
    }

    public function addProgramme(Programme $programme): static
    {
        if (!$this->programmes->contains($programme)) {
            $this->programmes->add($programme);
            $programme->setSession($this);
        }

        return $this;
    }

    public function removeProgramme(Programme $programme): static
    {
        if ($this->programmes->removeElement($programme)) {
            if ($programme->getSession() === $this) {
                $programme->setSession(null);
            }
        }

        return $this;
    }

 
    // Calcule des places restantes automatiquement
    public function getNbPlacesRestantes(): ?int
    {
        if ($this->placeDisponibles === null || $this->placeReservees === null) {
            return null;
        }

        return $this->placeDisponibles - $this->placeReservees;
    }


    //  Méthode pour savoir quand la session est complète
    public function estComplet(): bool
    {
        return $this->placeReservees >= $this->placeDisponibles;
    }


       // Méthode savoir la durée session : 
    public function DureeSession(): int
    {
        $debut = $this->getDateDebut();
        $fin = $this->getDateFin();

          if (!$debut || !$fin || $debut >= $fin) {
            return 0; 
            }

        // car dateperiod exclut la date de la fin
        $fin->modify('+1 day'); // ?

        $interval = $fin ->diff($debut);

        // jours total
        $jours = $interval -> days ;

        // create an iterateable period of date (P1D equates to 1 day)
        $periode = new DatePeriod($debut, new DateInterval('P1D'), $fin);

        $ferie = array('2025-01-01', '2025-04-21', '2025-05-01', '2025-05-08', '2025-05-29', 
                    '2025-06-09', '2025-07-14', '2025-08-15', '2025-11-01', '2025-11-11', '2025-12-25', '2025-12-26', '2025-04-18'); 
        // Les deux derniers c'est l'Alsace Moselle, + deux jours fériés

        foreach($periode as $dt) {
            $curr = $dt->format('D');
        
            // substract if Saturday or Sunday
        if ($curr == 'Sat' || $curr == 'Sun') {
            $jours--; // -- c'est pour soustraire 
        }

        elseif (in_array($dt->format('Y-m-d'), $ferie)) {
        $jours--;
        }
        
        }

        return $jours;
    }

}