<?php

namespace App\Form\Order;

use App\DTO\NewOrder;
use App\Entity\DeliveryTypeInterface;
use App\Entity\PaymentTypeInterface;
use App\Traits\Form\OrderTypeParamsTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderByOneClickType extends AbstractType
{
    use OrderTypeParamsTrait;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->buildProject($builder, $options);

        $builder->add('phone', TextType::class, ['constraints' => [new NotBlank()]]);
        $this->buildDeliveryType($builder);
        $this->buildItemsType($builder);

        $deliveryType = $options['delivery_type'] ?? null;

        $this->buildPaymentType($builder, $deliveryType);

        if ($deliveryType === DeliveryTypeInterface::DELIVERY_SHOP) {
            (new DeliveryShopType())->buildForm($builder, $options);
        }
    }

    public static function getPaymentTypes(): array
    {
        return [
            'Cash' => PaymentTypeInterface::PAYMENT_CASH,
        ];
    }

    /**
     * @return array
     */
    public static function getAvailableDeliveryTypes(): array
    {
        return [
            'Address' => DeliveryTypeInterface::DELIVERY_ADDRESS,
            'Shop' => DeliveryTypeInterface::DELIVERY_SHOP,
        ];
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
