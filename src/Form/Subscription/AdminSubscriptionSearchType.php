<?php

namespace App\Form\Subscription;

use App\DTO\Subscription\AdminSubscriptionSearch;
use App\Form\PaginationType;
use App\Form\ProjectType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;

class AdminSubscriptionSearchType extends ProjectType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('userRef', TextType::class);
        $builder->add('phone', TextType::class);
        $builder->add('firstName', TextType::class);
        $builder->add('lastName', TextType::class);
        $builder->add('phone', TextType::class);
        $builder->add('status', IntegerType::class);
        $builder->add('isActive', ChoiceType::class, ['choices' => [0, 1]]);

        $builder->add(
            'internalId',
            CollectionType::class,
            [
                'entry_type' => TextType::class,
                'allow_add' => true,
            ]
        );

        $builder->add('productId', TextType::class);

        if (empty($options['planning'])) {
            $builder->add(
                'startDateFrom',
                DateType::class,
                [
                    'property_path' => 'dateFrom',
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'constraints' => [
                        new Date(),
                    ],
                ]
            );

            $builder->add(
                'startDateTo',
                DateType::class,
                [
                    'property_path' => 'dateTo',
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'constraints' => [
                        new Date(),
                    ],
                ]
            );

            $builder->add('pagination', PaginationType::class);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'planning' => false,
                'data_class' => AdminSubscriptionSearch::class,
                'csrf_protection' => false,
            ]
        );
    }
}
