<?php

namespace App\Form\Order;

use App\DTO\NewAdminOrder;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminOrderByRegisteredType extends OrderByRegisteredType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('sendMessage', CheckboxType::class)
            ->add('userRef', IntegerType::class, ['constraints' => [new NotBlank()]]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'delivery_type' => '',
                'data_class' => NewAdminOrder::class,
                'csrf_protection' => false,
            ]
        );
    }
}
