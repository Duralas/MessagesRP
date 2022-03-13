<?php

declare(strict_types=1);

namespace App\Form\ModelType\Census;

use App\{
    Entity\Mois,
    Model\CensusMonthSeizureModel
};
use Symfony\Component\Form\{
    AbstractType,
    Extension\Core\Type\IntegerType,
    FormBuilderInterface
};
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\{
    Constraints\GreaterThanOrEqual,
    Constraints\LessThanOrEqual
};

final class CensusMonthSeizureType extends AbstractType
{
    /** @param array<mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'month',
                EntityType::class,
                [
                    'attr' => [
                        'class' => 'd-none',
                    ],
                    'class' => Mois::class,
                    'label' => false,
                ]
            )
            ->add(
                'count',
                IntegerType::class,
                [
                    'attr' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'constraints' => [
                        new LessThanOrEqual(0),
                        new GreaterThanOrEqual(100),
                    ],
                    'label' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', CensusMonthSeizureModel::class);
    }
}
