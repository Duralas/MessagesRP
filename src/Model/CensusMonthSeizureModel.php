<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Mois;

final class CensusMonthSeizureModel
{
    private Mois $month;

    private int $count = 0;

    public function __construct(Mois $month)
    {
        $this->month = $month;
    }

    public function getMonth(): Mois
    {
        return $this->month;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

        return $this;
    }
}
