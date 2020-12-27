<?php

namespace App\Repository;

use App\Entity\Personnage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Cache\CacheInterface;
use function is_string;

class PersonnageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personnage::class);
    }

    /**
     * Récupère les personnages à partir d'une liste.
     *
     * @param Personnage[]&Collection|string[] $personnages Liste à filtrer
     *
     * @return Personnage[] Personnages filtrés
     */
    public function findByList($personnages): array
    {
        $list = array_map(static function ($item) {
            return $item instanceof Personnage ? $item->getNom() : $item;
        }, $personnages instanceof Collection ? $personnages->toArray() : $personnages);

        return $this->createQueryBuilder('p')
            ->where('p.nom IN (:list)')
            ->setParameter('list', $list)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Récupère la query builder des personnages à partir d'un filtre en cache.
     *
     * @param CacheInterface $cache Cache applicatif
     *
     * @return QueryBuilder Requête des personnages à partir d'un filtre
     */
    public function getQBFromCachedPersonnages(CacheInterface $cache): QueryBuilder
    {
        $personnages = $cache->getItem('recensement_filters_personnages')->get();

        $qb = $this->createQueryBuilder('p');
        if (is_iterable($personnages) && !empty($personnages)) {
            $personnageList = array_map(static function ($item) {
                if ($item instanceof Personnage) {
                    return $item->getNom();
                }

                return is_string($item) && !empty($item) ? $item : null;
            }, $personnages instanceof Collection ? $personnages->toArray() : $personnages);

            if (!empty($personnageList)) {
                $qb->where('p.nom IN (:personnages)')
                    ->setParameter('personnages', $personnageList)
                ;
            }
        }

        return $qb;
    }
}
