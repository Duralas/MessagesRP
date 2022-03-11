<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Personnage;
use Doctrine\Common\Collections\Collection;

final class SeizureCensusModel
{
    private Personnage $personnage;

    /** @var Collection<CensusMonthSeizureModel> */
    private Collection $censusMonthSeizures;

    /** @param Collection<CensusMonthSeizureModel> $censusMonthSeizures */
    public function __construct(Personnage $personnage, Collection $censusMonthSeizures)
    {
        $this->personnage = $personnage;
        $this->censusMonthSeizures = $censusMonthSeizures;
    }

    public function getPersonnage(): Personnage
    {
        return $this->personnage;
    }

    /**
     * @return Collection<CensusMonthSeizureModel>
     */
    public function getCensusMonthSeizures(): Collection
    {
        return $this->censusMonthSeizures;
    }
}
