<?php

namespace App\Form;

use App\Entity\Message;
use App\Entity\Mois;
use App\Entity\Periode;
use App\Entity\Zone;
use App\Repository\PeriodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
        $this->em = $em;
        $this->cache = $cache;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Periode $periode */
        $periode = $options['periode'];

        $builder
            ->add('periode', EntityType::class, array(
                'class' => Periode::class,
                'data' => $periode,
                'query_builder' => static function (PeriodeRepository $repo) use ($periode) {
                    if ($periode) {
                        return $repo->getQBActive()
                            ->orWhere('p.code = :periode')
                            ->setParameter('periode', $periode->getCode())
                        ;
                    }

                    return $repo->getQBActive();
                }
            ))
            ->add('mois', EntityType::class, array(
                'class' => Mois::class,
                'data' => $this->em->getRepository(Mois::class)->find($options['mois']),
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
                'class' => Zone::class,
                'data' => $this->em->getRepository(Zone::class)->find($options['zone']),
                'group_by' => static function (Zone $zone) {
                  return $zone->getRegion() ? $zone->getRegion()->getNom() : '';
                },
            ))
            ->add('auteur')
            ->add('nombre', IntegerType::class, array(
                'data' => 1,
            ))
        ;

        $cache = $this->cache;
        $builder->addEventListener(FormEvents::POST_SUBMIT, static function (PostSubmitEvent $event) use ($cache) {
            /** @var Message $message */
            $message = $event->getData();

            // Mis en cache
            if (($mois = $message->getMois()) && ($zone = $message->getZone())) {
                $periodeKey = $message->getPeriode() ? $message->getPeriode()->getCode() : 'periode';

                $cachedMois = $cache->getItem("{$periodeKey}_mois");
                $cachedMois->set($mois->getOrdre());
                $cache->save($cachedMois);

                $cachedZone = $cache->getItem("{$periodeKey}_zone");
                $cachedZone->set($zone->getCode());
                $cache->save($cachedZone);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $periode = $this->periodeRepository->findActive();
        $periodeKey = $periode ? $periode->getCode() : 'periode';

        $resolver->setDefaults([
            'action' => '',
            'data_class' => Message::class,
            'periode' => $periode,
            'mois' => $this->cache->getItem("{$periodeKey}_mois")->get(),
            'zone' => $this->cache->getItem("{$periodeKey}_zone")->get(),
        ]);
    }
}
