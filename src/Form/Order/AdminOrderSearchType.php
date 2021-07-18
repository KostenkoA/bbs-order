<?php

namespace App\Form\Order;

use App\DTO\AdminOrderSearch;
use App\Form\PaginationType;
use App\Form\SortType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Length;

class AdminOrderSearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) :void
    {
        $builder
            ->add('phone')
            ->add('email')
            ->add('firstName')
            ->add('lastName')
            ->add('ref')
            ->add('number')
            ->add(
                'createDate',
                DateType::class,
                [
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'constraints' => [
                        new Date(),
                    ],
                ]
            )
            ->add(
                'createdAtFrom',
                DateTimeType::class,
                [
                    'widget' => 'single_text',
                    'constraints' => [
                        new Date(),
                    ],
                ]
            )
            ->add(
                'createdAtTo',
                DateTimeType::class,
                [
                    'widget' => 'single_text',
                    'constraints' => [
                        new Date(),
                    ],
                ]
            )
            ->add(
                'status',
                CollectionType::class,
                [
                    'allow_add' => true,
                    'entry_type' => ChoiceType::class,
                    'entry_options' => [
                        'choices' => AdminOrderSearch::availableStatuses(),
                        'documentation' => [
                            'type' => 'integer',
                            'description' => 'Order status',
                        ],
                    ],
                ]
            )
            ->add(
                'userRef',
                CollectionType::class,
                [
                    'allow_add' => true,
                    'entry_type' => TextType::class,
                    'entry_options' => [
                        'documentation' => [
                            'type' => 'string',
                            'description' => 'User id from ',
                        ],
                    ],
                ]
            )
            ->add(
                'projectName',
                TextType::class,
                [
                    'constraints' => [
                        new Length(['max' => 25]),
                    ],
                ]
            )
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
                'data_class' => AdminOrderSearch::class,
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
