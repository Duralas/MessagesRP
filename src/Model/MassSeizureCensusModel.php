<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Zone;
use Doctrine\Common\Collections\{
    ArrayCollection,
    Collection
};

final class MassSeizureCensusModel
{
    /** @var Collection<SeizureCensusModel> */
    private Collection $seizureCensusList;

    private ?Zone $zone = null;

    public function __construct()
    {
        $this->seizureCensusList = new ArrayCollection();
    }

    /**
     * @return Collection<SeizureCensusModel>
     */
    public function getSeizureCensusList(): Collection
    {
        return $this->seizureCensusList;
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
}
