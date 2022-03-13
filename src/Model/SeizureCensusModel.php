<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Personnage;

final class SeizureCensusModel
{
    private ?Personnage $personnage = null;

    /** @var array<CensusMonthSeizureModel> */
    private array $censusMonthSeizures = [];

    public function getPersonnage(): Personnage
    {
        return $this->personnage;
    }

    public function setPersonnage(?Personnage $personnage): static
    {
        $this->personnage = $personnage;

        return $this;
    }

    /**
     * @return array<CensusMonthSeizureModel>
     */
    public function getCensusMonthSeizures(): array
    {
        return $this->censusMonthSeizures;
    }

    /**
     * @param array<CensusMonthSeizureModel> $censusMonthSeizures
     */
    public function setCensusMonthSeizures(array $censusMonthSeizures): static
    {
        $this->censusMonthSeizures = $censusMonthSeizures;

        return $this;
    }

    public function addCensusMonthSeizure(CensusMonthSeizureModel $censusMonthSeizureModel): static
    {
        $this->censusMonthSeizures[] = $censusMonthSeizureModel;

        return $this;
    }
}
