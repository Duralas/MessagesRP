<?php

declare(strict_types=1);

namespace App\Form\ModelType\Census;

use App\Model\MassSeizureCensusModel;
use Symfony\Component\Form\{
    AbstractType,
    Extension\Core\Type\CollectionType,
    FormBuilderInterface
};
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
        $builder->add(
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
