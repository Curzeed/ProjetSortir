<?php

namespace App\Entity;

use App\Repository\CampusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CampusRepository::class)
 */
class Campus
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=Participant::class, mappedBy="campus")
     */
    private $estRattache;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="campus")
     */
    private $siteOrganisateur;

    public function __construct()
    {
        $this->estRattache = new ArrayCollection();
        $this->siteOrganisateur = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection|Participant[]
     */
    public function getEstRattache(): Collection
    {
        return $this->estRattache;
    }

    public function addEstRattache(Participant $estRattache): self
    {
        if (!$this->estRattache->contains($estRattache)) {
            $this->estRattache[] = $estRattache;
            $estRattache->setCampus($this);
        }

        return $this;
    }

    public function removeEstRattache(Participant $estRattache): self
    {
        if ($this->estRattache->removeElement($estRattache)) {
            // set the owning side to null (unless already changed)
            if ($estRattache->getCampus() === $this) {
                $estRattache->setCampus(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSiteOrganisateur(): Collection
    {
        return $this->siteOrganisateur;
    }

    public function addSiteOrganisateur(Sortie $siteOrganisateur): self
    {
        if (!$this->siteOrganisateur->contains($siteOrganisateur)) {
            $this->siteOrganisateur[] = $siteOrganisateur;
            $siteOrganisateur->setCampus($this);
        }

        return $this;
    }

    public function removeSiteOrganisateur(Sortie $siteOrganisateur): self
    {
        if ($this->siteOrganisateur->removeElement($siteOrganisateur)) {
            // set the owning side to null (unless already changed)
            if ($siteOrganisateur->getCampus() === $this) {
                $siteOrganisateur->setCampus(null);
            }
        }

        return $this;
    }
}
