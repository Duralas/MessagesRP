<?php

declare(strict_types=1);

namespace App\Form\ModelType\Census;

use App\{
    Entity\Personnage,
    Model\SeizureCensusModel
};
use Symfony\Component\Form\{
    AbstractType,
    Extension\Core\Type\CollectionType,
    Extension\Core\Type\IntegerType,
    FormBuilderInterface};
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

final class SeizureCensusType extends AbstractType
{
    /** @param array<mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'personnage',
                EntityType::class,
                [
                    'attr' => [
                        'class' => 'd-none',
                    ],
                    'class' => Personnage::class,
                    'choice_value' => static fn (?Personnage $personnage): ?string => ($personnage?->getNom()),
                    'label' => false,
                ]
            )
            ->add(
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
