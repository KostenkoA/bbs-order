<?php

namespace App\Form\Order;

use App\Interfaces\StreetTypeInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class DeliveryAddressType extends AbstractDeliveryType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('region', TextType::class, ['required' => false])
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
            )->add(
                'streetType',
                ChoiceType::class,
                [
                    'choices' => self::getAvailableStreetTypes(),
                ]
            )
            ->add('streetTypeName', TextType::class)
            ->add('streetRef', TextType::class)
            ->add('street', TextType::class)
            ->add('building', TextType::class)
            ->add('apartment', TextType::class);
    }

    public static function getAvailableStreetTypes(): array
    {
        return [
            '' => StreetTypeInterface::STREET_TYPE_EMPTY,
            'Улица' => StreetTypeInterface::STREET_TYPE_STREET,
            'Проспект' => StreetTypeInterface::STREET_TYPE_AVENUE,
            'Бульвар' => StreetTypeInterface::STREET_TYPE_BOULEVARD,
            'Площадь' => StreetTypeInterface::STREET_TYPE_SQUARE,
            'Переулок' => StreetTypeInterface::STREET_TYPE_LANE,
            'Набережная' => StreetTypeInterface::STREET_TYPE_EMBANKMENT,
            'Спуск' => StreetTypeInterface::STREET_TYPE_DESCENT,
            'Шоссе' => StreetTypeInterface::STREET_TYPE_HIGHWAY,
            'Микрорайон' => StreetTypeInterface::STREET_TYPE_MICRO_DISTRICT,
            'Жилой Массив' => StreetTypeInterface::STREET_TYPE_HOUSING_AREA,
            'Квартал' => StreetTypeInterface::STREET_TYPE_QUARTER,
            'Дорога' => StreetTypeInterface::STREET_TYPE_ROAD,
            'Аллея'=> StreetTypeInterface::STREET_TYPE_ALLEY,
            'Въезд'=> StreetTypeInterface::STREET_TYPE_ENTRY,
            'Линия'=> StreetTypeInterface::STREET_TYPE_LINE,
            'Майдан'=> StreetTypeInterface::STREET_TYPE_MAIDAN,
            'Пансионат'=> StreetTypeInterface::STREET_TYPE_PENSION,
            'Парк'=> StreetTypeInterface::STREET_TYPE_PARK,
            'Поселок'=> StreetTypeInterface::STREET_TYPE_VILLAGE,
            'Проезд'=> StreetTypeInterface::STREET_TYPE_PASSAGE,
            'Путь'=> StreetTypeInterface::STREET_TYPE_WAY,
            'Развилка'=> StreetTypeInterface::STREET_TYPE_FORK,
            'Роща'=> StreetTypeInterface::STREET_TYPE_GROVE,
            'Санаторий'=> StreetTypeInterface::STREET_TYPE_SANATORIUM,
            'Сквер'=> StreetTypeInterface::STREET_TYPE_SQUARE_1,
            'Cовхоз'=> StreetTypeInterface::STREET_TYPE_STAGE_FARM,
            'Станция'=> StreetTypeInterface::STREET_TYPE_STATION,
            'Тракт'=> StreetTypeInterface::STREET_TYPE_HIGHWAY_1,
            'Трасса'=> StreetTypeInterface::STREET_TYPE_ROUTE,
            'Тупик'=> StreetTypeInterface::STREET_TYPE_BLIND_ALLEY,
            'Узвоз'=> StreetTypeInterface::STREET_TYPE_DESCENT_1,
            'Урочище'=> StreetTypeInterface::STREET_TYPE_TRACT,
            'Хутор'=> StreetTypeInterface::STREET_TYPE_FARM,
            'Остров'=> StreetTypeInterface::STREET_TYPE_ISLAND,
        ];
    }
}
