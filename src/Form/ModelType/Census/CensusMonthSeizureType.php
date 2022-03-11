<?php

declare(strict_types=1);

namespace App\Form\ModelType\Census;

use App\Model\CensusMonthSeizureModel;
use Symfony\Component\Form\{
    AbstractType,
    Extension\Core\Type\IntegerType,
    FormBuilderInterface
};
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

final class CensusMonthSeizureType extends AbstractType
{
    /** @param array<mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'count',
            IntegerType::class,
            [
                'attr' => [
                    'min' => 0,
                ],
                'constraints' => [
                    new LessThanOrEqual(0),
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
