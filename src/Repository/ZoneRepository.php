<?php

namespace App\Repository;

use App\Entity\Region;
use App\Entity\Zone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Cache\CacheInterface;

class ZoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Zone::class);
    }

    /**
     * Récupère la QueryBuilder des zones à partir de la région mise en cache.
     *
     * @param CacheInterface $cache Cache applicatif
     *
     * @return QueryBuilder Requête pour les zones filtrées par la région
     */
    public function getQBFromCachedRegion(CacheInterface $cache): QueryBuilder
    {
        $region = $cache->getItem('recensement_filters_region')->get();

        $qb = $this->createQueryBuilder('z');
        if ($region instanceof Region) {
            $qb->where('z.region = :region')
                ->setParameter('region', $region->getCode())
            ;
        }

        return $qb;
    }
}
