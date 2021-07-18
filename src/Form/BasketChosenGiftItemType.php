<?php

namespace App\Form;

use App\DTO\Basket\BasketChosenGiftItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class BasketChosenGiftItemType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @throws \Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'internalId',
            TextType::class,
            ['constraints' => [new NotBlank()]]
        );
        $builder->add(
            'giftDiscountRef',
            TextType::class,
            ['constraints' => [new NotBlank()]]
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => BasketChosenGiftItem::class,
                'csrf_protection' => false,
            ]
        );
    }
}
