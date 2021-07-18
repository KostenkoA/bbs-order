<?php

namespace App\Form\Order;

use App\DTO\NewOrder;
use App\Form\BasketChosenGiftItemType;
use App\Form\BasketItemType;
use App\Traits\Form\OrderTypeParamsTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderType extends AbstractType
{
    use OrderTypeParamsTrait;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->buildProject($builder, $options);

        $this->buildCustomerParams($builder, $options);

        $builder->add(
            'items',
            CollectionType::class,
            [
                'required' => true,
                'property_path' => 'orderItems',
                'entry_type' => BasketItemType::class,
                'allow_add' => true,
            ]
        );

        $builder->add(
            'chosenGiftItems',
            CollectionType::class,
            [
                'entry_type' => BasketChosenGiftItemType::class,
                'allow_add' => true,
            ]
        );

        if (isset($options['delivery_type'])) {
            $this->buildDelivery($builder, $options, $options['delivery_type']);
            $this->buildPaymentType($builder, $options['delivery_type']);
        }

        $builder->add('comment');
        $builder->add('callBack', ChoiceType::class, ['choices' => [1, 0]]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'delivery_type' => '',
                'data_class' => NewOrder::class,
                'csrf_protection' => false,
            ]
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return '';
    }
}
