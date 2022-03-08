<?php

namespace App\Repository;

use App\{
    Entity\Faction,
    Entity\Personnage
};
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Cache\CacheInterface;

class PersonnageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personnage::class);
    }

    /**
     * @return array<Personnage>
     */
    public function getAll(): array
    {
        return $this
            ->getQBBase()
            ->getQuery()
            ->getResult();
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

        return $this->getQBBase()
            ->andWhere('p.nom IN (:list)')
            ->setParameter('list', $list)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Récupère la query builder de base pour les personnages :
     * - Exclusion des archives.
     * - Ordre par activité et par nom.
     *
     * @return QueryBuilder Requête de base des personnages
     */
    public function getQBBase(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.archive = false')
            ->orderBy('p.actif', 'DESC')
            ->addOrderBy('p.nom')
        ;
    }

    /**
     * Récupère la query builder des personnages à partir de filtres en cache.
     *
     * @param CacheInterface $cache Cache applicatif
     *
     * @return QueryBuilder Requête des personnages à partir de filtres
     */
    public function getQBFromCachedPersonnages(CacheInterface $cache): QueryBuilder
    {
        $qb = $this->getQBBase();

        // Requête via le cache
        $whereClause = array();

        // Personnages
        $personnages = $cache->getItem('recensement_filters_personnages')->get();
        if (is_iterable($personnages) && !empty($personnages)) {
            $personnageList = array_map(static function ($item) {
                if ($item instanceof Personnage) {
                    return $item->getNom();
                }

                return \is_string($item) && !empty($item) ? $item : null;
            }, $personnages instanceof Collection ? $personnages->toArray() : $personnages);

            if (!empty($personnageList)) {
                $whereClause[] = 'p.nom IN (:personnages)';
                $qb->setParameter('personnages', $personnageList);
            }
        }

        // Faction
        $faction = $cache->getItem('recensement_filters_faction')->get();
        if ($faction instanceof Faction) {
            $whereClause[] = 'p.faction = :faction';
            $qb->setParameter('faction', $faction->getCode());
        }

        // Ajout de la requête du cache
        if (!empty($whereClause)) {
            $qb->andWhere(implode(' OR ', $whereClause));
        }

        return $qb;
    }
}
