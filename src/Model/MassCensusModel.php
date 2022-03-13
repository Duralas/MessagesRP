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

    /** @var Collection<Personnage> */
    private Collection $personnages;

    public function __construct()
    {
        $this->personnages = new ArrayCollection();
    }

    public function getPeriode(): ?Periode
    {
        return $this->periode;
    }

    public function getStrictPeriode(): Periode
    {
        if ($this->getPeriode() instanceof Periode === false) {
            throw new \TypeError(
                'Given type "'
                . get_debug_type($this->getPeriode())
                . '" but expected "'
                . Periode::class
                . '".'
            );
        }

        return $this->getPeriode();
    }

    public function setPeriode(?Periode $periode): static
    {
        $this->periode = $periode;

        return $this;
    }

    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    public function setZone(?Zone $zone): static
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
    public function setPersonnages(Collection $personnages): static
    {
        $this->personnages = $personnages;

        return $this;
    }
}
