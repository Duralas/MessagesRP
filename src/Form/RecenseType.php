<?php

namespace App\Form;

use App\Entity\Message;
use App\Entity\Mois;
use App\Entity\Periode;
use App\Entity\Zone;
use App\Repository\PeriodeRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecenseType extends AbstractType
{
    /**
     * @var PeriodeRepository
     */
    private PeriodeRepository $periodeRepository;

    public function __construct(PeriodeRepository $periodeRepository)
    {
        $this->periodeRepository = $periodeRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Periode $periode */
        $periode = $options['periode'];

        $builder
            ->add('periode', EntityType::class, array(
                'class' => Periode::class,
                'query_builder' => static function (PeriodeRepository $repo) {
                    return $repo->getQBActive();
                }
            ))
            ->add('mois', EntityType::class, array(
                'class' => Mois::class,
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
                'group_by' => static function (Zone $zone) {
                  return $zone->getRegion() ? $zone->getRegion()->getNom() : '';
                },
            ))
            ->add('auteur')
            ->add('nombre', IntegerType::class, array(
                'data' => 1,
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'action' => '',
            'data_class' => Message::class,
            'periode' => $this->periodeRepository->findActive(),
        ]);
    }
}
