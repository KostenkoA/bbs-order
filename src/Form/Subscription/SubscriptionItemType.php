<?php

namespace App\Form\Subscription;

use App\DTO\Subscription\SubscriptionItem;
use App\Form\BasketItemType;
use Exception;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class SubscriptionItemType extends BasketItemType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add(
            'isActive',
            ChoiceType::class,
            [
                'choices' => [0, 1],
            ]
        );

        $builder->add(
            'intervalDays',
            IntegerType::class,
            [
                'constraints' => [
                    new NotBlank(),
                    new GreaterThan(0),
                ],
            ]
        );

        $builder->add(
            'startDate',
            DateType::class,
            [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'constraints' => [new Date(), new NotBlank()],
            ]
        );

        $builder->add(
            'skipDateFrom',
            DateType::class,
            [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'constraints' => [new Date()],
            ]
        );

        $builder->add(
            'skipDateTo',
            DateType::class,
            [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'constraints' => [new Date()],
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
                'data_class' => SubscriptionItem::class,
                'csrf_protection' => false,
            ]
        );
    }
}
