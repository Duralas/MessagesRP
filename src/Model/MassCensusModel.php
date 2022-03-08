<?php

declare(strict_types=1);

namespace App\Model;

use App\{
    Entity\Periode,
    Entity\Personnage,
    Entity\Zone
};
use Doctrine\Common\Collections\{
    ArrayCollection,
    Collection
};

final class MassCensusModel
{
    private ?Periode $periode = null;

    private ?Zone $zone = null;

    private Collection $personnages;

    public function __construct()
    {
        $this->personnages = new ArrayCollection();
    }

    public function getPeriode(): ?Periode
    {
        return $this->periode;
    }

    public function setPeriode(?Periode $periode): MassCensusModel
    {
        $this->periode = $periode;

        return $this;
    }

    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    public function setZone(?Zone $zone): MassCensusModel
    {
        $this->zone = $zone;

        return $this;
    }

    /**
     * @return Collection<Personnage>
     */
    public function getPersonnages(): Collection
    {
        return $this->personnages;
    }

    /**
     * @param Collection<Personnage> $personnages
     */
    public function setPersonnages(Collection $personnages): MassCensusModel
    {
        $this->personnages = $personnages;

        return $this;
    }
}
