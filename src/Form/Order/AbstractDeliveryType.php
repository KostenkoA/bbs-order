<?php

namespace App\Form\Order;

use App\DTO\NewOrder;
use App\Entity\DeliveryCarrierInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

abstract class AbstractDeliveryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'deliveryCarrier',
                ChoiceType::class,
                [
                    'choices' => self::getAvailableCarriers(),
                    'constraints' => [
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
                'data_class' => NewOrder::class,
                'csrf_protection' => false,
            ]
        );
    }

    /**
     * @return int[]
     */
    public static function getAvailableCarriers(): array
    {
        return [
            '' => DeliveryCarrierInterface::CARRIER_DEFAULT_CARRIER,
            'MeestExpress' => DeliveryCarrierInterface::CARRIER_MEEST_EXPRESS,
            'NovaPoshta' => DeliveryCarrierInterface::CARRIER_NOVA_POSHTA,
            'EasyPost' => DeliveryCarrierInterface::CARRIER_EASY_POST,
            'SmartPost' => DeliveryCarrierInterface::CARRIER_SMART_POST,
            'Justin' => DeliveryCarrierInterface::CARRIER_JUSTIN,
            'IPost' => DeliveryCarrierInterface::CARRIER_IPOST,
        ];
    }
}
