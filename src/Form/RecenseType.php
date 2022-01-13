<?php

namespace App\Form;

use App\Entity\Message;
use App\Entity\Mois;
use App\Entity\Periode;
use App\Entity\Personnage;
use App\Entity\Zone;
use App\Repository\PeriodeRepository;
use App\Repository\PersonnageRepository;
use App\Repository\ZoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Cache\CacheInterface;

class RecenseType extends AbstractType
{
    /**
     * @var PeriodeRepository
     */
    private PeriodeRepository $periodeRepository;

    /**
     * @var CacheInterface
     */
    private CacheInterface $cache;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em, CacheInterface $cache)
    {
        $this->periodeRepository = $em->getRepository(Periode::class);
        $this->em                = $em;
        $this->cache             = $cache;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Periode $periode */
        $periode  = $options['periode'];
        $appCache = $this->cache;

        $mois = $options['mois'];
        $zone = $options['zone'];
        $builder
            ->add('periode', EntityType::class, array(
                'class'         => Periode::class,
                'data'          => $periode,
//                'query_builder' => static function (PeriodeRepository $repo) use ($periode) {
//                    return $repo->getQBActive($periode);
//                },
            ))
            ->add('mois', EntityType::class, array(
                'class'         => Mois::class,
                'data'          => $mois ? $this->em->getRepository(Mois::class)->find($mois) : null,
                'query_builder' => static function (EntityRepository $repo) use ($periode) {
                    $qb = $repo->createQueryBuilder('m')->orderBy('m.ordre');

                    if ($periode instanceof Periode && $periode->getStartDate() && $periode->getEndDate()) {
                        $qb->where('m.ordre >= :first_month and m.ordre <= :last_month')
                            ->setParameter('first_month', $periode->getStartDate()->format('m'))
                            ->setParameter('last_month', $periode->getEndDate()->format('m'))
                        ;
                    }

                    return $qb;
                },
            ))
            ->add('zone', EntityType::class, array(
                'class'    => Zone::class,
                'data'     => $zone ? $this->em->getRepository(Zone::class)->find($zone) : null,
                'group_by' => static function (Zone $zone) {
                    return $zone->getRegion() ? $zone->getRegion()->getNom() : '';
                },
                'query_builder' => static function (ZoneRepository $repo) use ($appCache) {
                    return $repo->getQBFromCachedRegion($appCache);
                },
            ))
            ->add('auteur', EntityType::class, array(
                'class'    => Personnage::class,
                'group_by' => static function (Personnage $personnage) {
                    return $personnage->isActif() ? 'Actif' : 'Inactif';
                },
                'query_builder' => static function (PersonnageRepository $repo) use ($appCache) {
                    return $repo->getQBFromCachedPersonnages($appCache);
                },
            ))
            ->add('nombre')
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, static function (PostSubmitEvent $event) use ($appCache) {
            /** @var Message $message */
            $message = $event->getData();

            // Mise en cache
            if (($mois = $message->getMois()) && ($zone = $message->getZone())) {
                $periodeKey = $message->getPeriode() ? $message->getPeriode()->getCode() : 'periode';

                $cachedMois = $appCache->getItem("{$periodeKey}_mois");
                $cachedMois->set($mois->getOrdre());
                $appCache->save($cachedMois);

                $cachedZone = $appCache->getItem("{$periodeKey}_zone");
                $cachedZone->set($zone->getCode());
                $appCache->save($cachedZone);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $periode    = $this->periodeRepository->findActive();
        $periodeKey = $periode ? $periode->getCode() : 'periode';

        $resolver->setDefaults(array(
            'data_class' => Message::class,
            'periode'    => $periode,
            'mois'       => $this->cache->getItem("{$periodeKey}_mois")->get(),
            'zone'       => $this->cache->getItem("{$periodeKey}_zone")->get(),
        ));

        $resolver->setAllowedTypes('periode', array(Periode::class, 'null'));
    }
}
