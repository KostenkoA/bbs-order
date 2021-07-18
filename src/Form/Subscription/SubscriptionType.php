<?php

namespace App\Form\Subscription;

use App\DTO\Subscription\Subscription;
use App\Form\ProjectType;
use App\Traits\Form\OrderTypeParamsTrait;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionType extends ProjectType
{
    use OrderTypeParamsTrait;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $this->buildCustomerParams($builder, $options);

        if (isset($options['delivery_type'])) {
            $this->buildDelivery($builder, $options, $options['delivery_type']);
            $this->buildPaymentType($builder, $options['delivery_type']);
        }

        $builder->add(
            'isActive',
            ChoiceType::class,
            [
                'choices' => [0, 1],
            ]
        );

        $builder->add(
            'items',
            CollectionType::class,
            [
                'entry_type' => SubscriptionItemType::class,
                'allow_add' => true,
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
                'delivery_type' => '',
                'data_class' => Subscription::class,
                'csrf_protection' => false,
            ]
        );
    }
}
