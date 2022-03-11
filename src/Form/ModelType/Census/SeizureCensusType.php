<?php

declare(strict_types=1);

namespace App\Form\ModelType\Census;

use App\Model\SeizureCensusModel;
use Symfony\Component\Form\{
    AbstractType,
    Extension\Core\Type\CollectionType,
    FormBuilderInterface
};
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SeizureCensusType extends AbstractType
{
    /** @param array<mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'censusMonthSeizures',
            CollectionType::class,
            [
                'by_reference' => false,
                'entry_type' => CensusMonthSeizureType::class,
                'label' => false,
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', SeizureCensusModel::class);
    }
}
