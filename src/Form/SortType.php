<?php


namespace App\Form;

use App\DTO\Sort;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder->add('field', TextType::class)
            ->add(
                'type',
                ChoiceType::class,
                [
                    'choices' => [
                        'ASC' => 'ASC',
                        'DESC' => 'DESC',
                    ],
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) :void
    {
        $resolver->setDefaults(
            [
                'data_class' => Sort::class,
                'csrf_protection' => false,
            ]
        );
    }
}
