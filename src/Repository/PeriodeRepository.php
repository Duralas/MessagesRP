<?php


namespace App\Repository;


use App\Entity\Periode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\{
    AbstractQuery,
    QueryBuilder
};
use Doctrine\Persistence\ManagerRegistry;

final class PeriodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Periode::class);
    }

    /**
     * @return array<string>
     */
    public function getAllActiveIds(): array
    {
        return $this
            ->getQBActive()
            ->select('p.code')
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_SCALAR_COLUMN);
    }

    public function findActive(): ?Periode
    {
        return $this->getQBActive()->getQuery()->getOneOrNullResult();
    }

    public function getQBActive(?Periode $orPeriode = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.startDate <= :today AND p.endDate >= :today')
            ->setParameter('today', date_create())
        ;

        if ($orPeriode) {
            $qb->orWhere('p.code = :periode')
                ->setParameter('periode', $orPeriode->getCode())
            ;
        }

        return $qb;
    }
}
