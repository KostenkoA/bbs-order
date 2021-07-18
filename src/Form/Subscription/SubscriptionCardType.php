<?php

namespace App\Form\Subscription;

use App\DTO\Subscription\SubscriptionCard;
use App\Form\ProjectType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SubscriptionCardType extends ProjectType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add(
            'cardHash',
            TextType::class,
            [
                'constraints' => [new NotBlank()],
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
                'data_class' => SubscriptionCard::class,
                'csrf_protection' => false,
            ]
        );
    }
}
