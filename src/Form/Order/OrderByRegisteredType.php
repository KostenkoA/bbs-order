<?php

namespace App\Form\Order;

use App\Entity\DeliveryTypeInterface;
use App\Form\Subscription\SubscriptionItemType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class OrderByRegisteredType extends OrderType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        if (isset($options['delivery_type'])) {
            $this->buildBonus($builder, $options['delivery_type']);
        }

        $builder->add(
            'subscriptionItems',
            CollectionType::class,
            [
                'entry_type' => SubscriptionItemType::class,
                'allow_add' => true,
            ]
        );
    }
}
