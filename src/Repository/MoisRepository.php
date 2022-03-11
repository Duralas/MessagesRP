<?php

namespace App\Repository;

use App\{
    Entity\Periode,
    Entity\Mois
};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MoisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mois::class);
    }

    /** @return array<Mois> */
    public function findForPeriode(Periode $periode): array
    {
        $datePeriod = new \DatePeriod(
            $periode->getStartDate(),
            \DateInterval::createFromDateString('1 month'),
            $periode->getEndDate()
        );
        $monthList = [];
        foreach ($datePeriod as $monthInPeriod) {
            $monthList[] = $monthInPeriod->format('n');
        }

        return $this
            ->createQueryBuilder('m')
            ->where('m.ordre IN (:month_list)')
            ->setParameter('month_list', $monthList)
            ->orderBy('m.ordre')
            ->getQuery()
            ->getResult();
    }
}
