<?php

declare(strict_types=1);

namespace App\Model;

use App\{
    Entity\Periode,
    Entity\Zone
};

final class MassSeizureCensusModel
{
    /** @var array<SeizureCensusModel> */
    private array $seizureCensusList = [];

    private ?Periode $periode = null;

    private ?Zone $zone = null;

    /**
     * @return array<SeizureCensusModel>
     */
    public function getSeizureCensusList(): array
    {
        return $this->seizureCensusList;
    }

    /**
     * @param array<SeizureCensusModel> $seizureCensusList
     */
    public function setSeizureCensusList(array $seizureCensusList): static
    {
        $this->seizureCensusList = $seizureCensusList;

        return $this;
    }

    public function addSeizureCensus(SeizureCensusModel $seizureCensusModel): static
    {
        $this->seizureCensusList[] = $seizureCensusModel;

        return $this;
    }

    public function getPeriode(): ?Periode
    {
        return $this->periode;
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
}
