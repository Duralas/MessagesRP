<?php

namespace App\Repository;

use App\{
    Entity\Periode,
    Entity\Mois
};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

final class MoisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mois::class);
    }

    /**
     * @return array<int>
     */
    public function getAllIds(): array
    {
        return $this
            ->createQueryBuilder('m')
            ->select('m.ordre')
            ->orderBy('m.ordre')
            ->getQuery()
            ->enableResultCache()
            ->getResult(AbstractQuery::HYDRATE_SCALAR_COLUMN);
    }

    public function findByOrder(int $order): ?Mois
    {
        return $this
            ->createQueryBuilder('m')
            ->where('m.ordre = :order')
            ->setParameter('order', $order)
            ->getQuery()
            ->enableResultCache()
            ->getOneOrNullResult();
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
