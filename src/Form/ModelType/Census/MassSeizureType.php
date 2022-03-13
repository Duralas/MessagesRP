<?php

declare(strict_types=1);

namespace App\Form\ModelType\Census;

use App\{
    Entity\Periode,
    Entity\Zone,
    Model\MassSeizureCensusModel
};
use Symfony\Component\Form\{
    AbstractType,
    Extension\Core\Type\CollectionType,
    FormBuilderInterface
};
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

final class MassSeizureType extends AbstractType
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
                    'attr' => [
                        'class' => 'd-none',
                    ],
                    'class' => Periode::class,
                    'choice_value' => static fn (?Periode $periode): ?string => ($periode?->getCode()),
                    'label' => false,
                ]
            )
            ->add(
                'zone',
                EntityType::class,
                [
                    'attr' => [
                        'class' => 'd-none',
                    ],
                    'class' => Zone::class,
                    'choice_value' => static fn (?Zone $zone): ?string => ($zone?->getCode()),
                    'label' => false,
                ]
            )
            ->add(
                'seizureCensusList',
                CollectionType::class,
                [
                    'by_reference' => false,
                    'entry_type' => SeizureCensusType::class,
                    'label' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('data_class', MassSeizureCensusModel::class)
            ->setDefault('action', $this->router->generate('app_census_mass_seizure_post'));
    }
}
