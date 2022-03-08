<?php

declare(strict_types=1);

namespace App\Form\ModelType\Census;

use App\{
    Entity\Periode,
    Entity\Personnage,
    Entity\Zone,
    Model\MassCensusModel,
    Repository\PeriodeRepository,
    Repository\PersonnageRepository
};
use Symfony\Component\Form\{
    AbstractType,
    FormBuilderInterface
};
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\{
    Constraints\Count,
    Constraints\NotNull
};

final class MassType extends AbstractType
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /** @param array<string, mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'periode',
                EntityType::class,
                [
                    'class' => Periode::class,
                    'constraints' => [
                        new NotNull(message: 'Aucune période active.'),
                    ],
                    'query_builder' => static fn (PeriodeRepository $periodeRepository): QueryBuilder => (
                        $periodeRepository->getQBActive()
                    ),
                    'required' => false,
                ]
            )
            ->add(
                'zone',
                EntityType::class,
                [
                    'class' => Zone::class,
                    'constraints' => [
                        new NotNull(message: 'Une zone est nécessaire.'),
                    ],
                    'group_by' => static fn (Zone $zone): string => (
                        $zone->getRegion()?->getNom() ?? ''
                    ),
                    'required' => false,
                ]
            )
            ->add(
                'personnages',
                EntityType::class,
                array(
                    'class' => Personnage::class,
                    'constraints' => [
                        new Count(min: 1, minMessage: 'Au moins un personnage doit être sélectionné.'),
                    ],
                    'group_by' => static fn (Personnage $personnage): string => (
                        $personnage->isActif()
                            ? ($personnage->getFaction()?->getNom() ?? 'Sans faction')
                            : 'Inactif'
                    ),
                    'multiple' => true,
                    'query_builder' => static fn (PersonnageRepository $personnageRepository): QueryBuilder => (
                        $personnageRepository->getQBBase()
                    ),
                    'required' => false,
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('data_class', MassCensusModel::class)
            ->setDefault('action', $this->router->generate('app_census_mass_seizure_get'))
            ->setDefault('method', 'GET');
    }
}
