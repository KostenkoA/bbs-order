<?php

namespace App\Form\Subscription;

use App\DTO\Subscription\AdminSubscriptionSearch;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminSubscriptionPlanningType extends AdminSubscriptionSearchType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add(
            'dateFrom',
            DateType::class,
            [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'constraints' => [
                    new Date(),
                    new NotBlank(),
                ],
            ]
        );

        $builder->add(
            'dateTo',
            DateType::class,
            [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'constraints' => [
                    new Date(),
                    new NotBlank(),
                ],
            ]
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'planning' => true,
                'data_class' => AdminSubscriptionSearch::class,
                'csrf_protection' => false,
            ]
        );
    }
}
