<?php

namespace App\Form;

use App\DTO\Basket\Basket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BasketType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('project', TextType::class)
            ->add('phone', TextType::class)
            ->add('bonus', IntegerType::class)
            ->add(
                'items',
                CollectionType::class,
                [
                    'required' => true,
                    'property_path' => 'basketItems',
                    'entry_type' => BasketItemType::class,
                    'allow_add' => true,
                ]
            )
            ->add(
                'certificates',
                CollectionType::class,
                ['entry_type' => TextType::class]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Basket::class,
                'csrf_protection' => false,
                'allow_extra_fields' => true,
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
