<?php

namespace App\Form\Order;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DeliveryBranchType extends AbstractDeliveryType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('region', TextType::class, ['required' => false])
            ->add('district', TextType::class, ['required' => false])
            ->add('cityRef', TextType::class)
            ->add(
                'city',
                TextType::class,
                [
                    'required' => true,
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add('deliveryBranch', TextType::class)
            ->add('deliveryBranchRef', TextType::class);
    }
}
