<?php

namespace App\Traits\Form;

use App\Entity\DeliveryTypeInterface;
use App\Entity\PaymentTypeInterface;
use App\Form\BasketItemType;
use App\Form\Order\DeliveryAddressType;
use App\Form\Order\DeliveryBranchType;
use App\Form\Order\DeliveryShopType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

trait OrderTypeParamsTrait
{
    protected function buildProject(FormBuilderInterface $builder, array $options):void
    {
        $builder->add('project', TextType::class, ['constraints' => [new NotBlank()]]);
    }

    protected function buildCustomerParams(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('firstName', TextType::class, ['constraints' => [new NotBlank()]])
            ->add('lastName', TextType::class)
            ->add('middleName', TextType::class)
            ->add('phone', TextType::class, ['constraints' => [new NotBlank()]])
            ->add('email', EmailType::class);
    }

    protected function buildDelivery(FormBuilderInterface $builder, array $options, $deliveryType): void
    {
        $builder->add(
            'deliveryType',
            ChoiceType::class,
            [
                'choices' => static::getAvailableDeliveryTypes(),
            ]
        );

        switch ($deliveryType) {
            case DeliveryTypeInterface::DELIVERY_BRANCH:
                (new DeliveryBranchType())->buildForm($builder, $options);
                break;
            case DeliveryTypeInterface::DELIVERY_ADDRESS:
                (new DeliveryAddressType())->buildForm($builder, $options);
                break;
            case DeliveryTypeInterface::DELIVERY_SHOP:
                (new DeliveryShopType())->buildForm($builder, $options);
                break;
        }
    }

    protected function buildItemsType(FormBuilderInterface $builder): void
    {
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
    }

    protected function buildDeliveryType(FormBuilderInterface $builder): void
    {
        $builder->add(
            'deliveryType',
            ChoiceType::class,
            [
                'choices' => static::getAvailableDeliveryTypes(),
            ]
        );
    }

    protected function buildPaymentType(FormBuilderInterface $builder, $deliveryType): void
    {
        $choices = (int)$deliveryType === DeliveryTypeInterface::DELIVERY_SHOP ?
            static::getDeliveryShopPaymentTypes() : static::getPaymentTypes();

        $builder->add('paymentType', ChoiceType::class, ['choices' => $choices]);
    }

    protected function buildBonus(FormBuilderInterface $builder, $deliveryType): void
    {
        if ((int)$deliveryType !== DeliveryTypeInterface::DELIVERY_SHOP) {
            $builder->add(
                'usedBonuses',
                IntegerType::class,
                [
                    'constraints' => [
                        new GreaterThanOrEqual(0),
                    ],
                ]
            );
        }
    }

    protected function buildLanguageId(FormBuilderInterface $builder): void
    {
        $builder->add('userLanguageId', IntegerType::class, ['constraints' => [new NotBlank()]]);
    }

    /**
     * @return array
     */
    public static function getAvailableDeliveryTypes(): array
    {
        return [
            'Branch' => DeliveryTypeInterface::DELIVERY_BRANCH,
            'Address' => DeliveryTypeInterface::DELIVERY_ADDRESS,
            'Shop' => DeliveryTypeInterface::DELIVERY_SHOP,
        ];
    }

    /**
     * @return array
     */
    public static function getPaymentTypes(): array
    {
        return [
            'Cash' => PaymentTypeInterface::PAYMENT_CASH,
            'Card' => PaymentTypeInterface::PAYMENT_CARD,
        ];
    }

    public static function getDeliveryShopPaymentTypes(): array
    {
        return [
            'Cash' => PaymentTypeInterface::PAYMENT_CASH,
            'CardInShop' => PaymentTypeInterface::PAYMENT_CARD_SHOP,
        ];
    }
}
