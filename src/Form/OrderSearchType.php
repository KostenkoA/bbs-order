<?php

namespace App\Form;

use App\DTO\OrderSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
            ->add(
                'sort',
                CollectionType::class,
                [
                    'entry_type' => SortType::class,
                    'allow_add' => true,
                ]
            )
            ->add('pagination', PaginationType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) :void
    {
        $resolver->setDefaults(
            [
                'data_class' => OrderSearch::class,
                'csrf_protection' => false,
            ]
        );
    }

    /**
     * @return string
     */
    public function getBlockPrefix() :string
    {
        return '';
    }
}
