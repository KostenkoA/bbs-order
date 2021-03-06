<?php

namespace App\Form;

use App\DTO\Pagination;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;

class PaginationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
            ->add(
                'page',
                IntegerType::class,
                [
                    'empty_data' => 1,
                    'constraints' => [
                        new GreaterThan(0),
                    ],

                ]
            )
            ->add(
                'limit',
                IntegerType::class,
                [
                    'empty_data' => 20,
                    'constraints' => [
                        new GreaterThan(0),
                    ],
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) :void
    {
        $resolver->setDefaults(
            [
                'data_class' => Pagination::class,
                'csrf_protection' => false,
            ]
        );
    }
}
