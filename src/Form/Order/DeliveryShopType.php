<?php

namespace App\Form\Order;

use App\Interfaces\StreetTypeInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DeliveryShopType extends AbstractDeliveryType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'deliveryShop',
            TextType::class,
            [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );
    }
}
