<?php

namespace App\Entity\Traits;

use App\Interfaces\StreetTypeInterface;

/**
 * Trait StreetTypeTrait
 * @package App\Entity\Traits
 */
trait StreetTypeTrait
{
    private static $streetTypeNames = [
        StreetTypeInterface::STREET_TYPE_STREET => [
            'name' => 'Улица',
            'shortName' => 'ул.',
            'nameUkr' => 'Вулиця',
            'shortNameUkr' => 'вул.',
        ],
        StreetTypeInterface::STREET_TYPE_AVENUE => [
            'name' => 'Проспект',
            'shortName' => 'просп.',
            'nameUkr' => 'Проспект',
            'shortNameUkr' => 'просп.',
        ],
        StreetTypeInterface::STREET_TYPE_BOULEVARD => [
            'name' => 'Бульвар',
            'shortName' => 'бул.',
            'nameUkr' => 'Бульвар',
            'shortNameUkr' => 'бул.',
        ],
        StreetTypeInterface::STREET_TYPE_SQUARE => [
            'name' => 'Площадь',
            'shortName' => 'пл.',
            'nameUkr' => 'Площа',
            'shortNameUkr' => 'пл.',
        ],
        StreetTypeInterface::STREET_TYPE_LANE => [
            'name' => 'Переулок',
            'shortName' => 'переулок',
            'nameUkr' => 'Провулок',
            'shortNameUkr' => 'пров.',
        ],
        StreetTypeInterface::STREET_TYPE_EMBANKMENT => [
            'name' => 'Набережная',
            'shortName' => 'наб.',
            'nameUkr' => 'Набережна',
            'shortNameUkr' => 'наб.',
        ],
        StreetTypeInterface::STREET_TYPE_DESCENT => [
            'name' => 'Спуск',
            'shortName' => 'спуск',
            'nameUkr' => 'Спуск',
            'shortNameUkr' => 'спуск',
        ],
        StreetTypeInterface::STREET_TYPE_HIGHWAY => [
            'name' => 'Шоссе',
            'shortName' => 'шоссе',
            'nameUkr' => 'Шосе',
            'shortNameUkr' => 'шосе',
        ],
        StreetTypeInterface::STREET_TYPE_MICRO_DISTRICT => [
            'name' => 'Микрорайон',
            'shortName' => 'микр.',
            'nameUkr' => 'Мікрорайон',
            'shortNameUkr' => 'мікр.',
        ],
        StreetTypeInterface::STREET_TYPE_HOUSING_AREA => [
            'name' => 'Жилой Массив',
            'shortName' => 'ж/м',
            'nameUkr' => 'Житловий Масив',
            'shortNameUkr' => 'ж/м',
        ],
        StreetTypeInterface::STREET_TYPE_QUARTER => [
            'name' => 'Квартал',
            'shortName' => 'кв.',
            'nameUkr' => 'Квартал',
            'shortNameUkr' => 'кв.',
        ],
        StreetTypeInterface::STREET_TYPE_ROAD => [
            'name' => 'Дорога',
            'shortName' => 'дорога',
            'nameUkr' => 'Дорога',
            'shortNameUkr' => 'дорога',
        ],
        StreetTypeInterface::STREET_TYPE_ALLEY => [
            'name' => 'Аллея',
            'shortName' => 'аллея',
            'nameUkr' => 'Алея',
            'shortNameUkr' => 'алея',
        ],
        StreetTypeInterface::STREET_TYPE_ENTRY => [
            'name' => 'Въезд',
            'shortName' => 'въезд',
            'nameUkr' => 'В`їзд',
            'shortNameUkr' => 'в`їзд',
        ],
        StreetTypeInterface::STREET_TYPE_LINE => [
            'name' => 'Линия',
            'shortName' => 'линия',
            'nameUkr' => 'Лінія',
            'shortNameUkr' => 'лінія',
        ],
        StreetTypeInterface::STREET_TYPE_MAIDAN => [
            'name' => 'Майдан',
            'shortName' => 'майдан',
            'nameUkr' => 'Майдан',
            'shortNameUkr' => 'майдан',
        ],
        StreetTypeInterface::STREET_TYPE_PENSION => [
            'name' => 'Пансионат',
            'shortName' => 'панс.',
            'nameUkr' => 'Пансіонат',
            'shortNameUkr' => 'панс.',
        ],
        StreetTypeInterface::STREET_TYPE_PARK => [
            'name' => 'Парк',
            'shortName' => 'парк',
            'nameUkr' => 'Парк',
            'shortNameUkr' => 'парк',
        ],
        StreetTypeInterface::STREET_TYPE_VILLAGE => [
            'name' => 'Поселок',
            'shortName' => 'пос.',
            'nameUkr' => 'Селище',
            'shortNameUkr' => 'селище',
        ],
        StreetTypeInterface::STREET_TYPE_PASSAGE => [
            'name' => 'Проезд',
            'shortName' => 'проезд',
            'nameUkr' => 'Проїзд',
            'shortNameUkr' => 'проїзд',
        ],
        StreetTypeInterface::STREET_TYPE_WAY => [
            'name' => 'Путь',
            'shortName' => 'путь',
            'nameUkr' => 'Шлях',
            'shortNameUkr' => 'шлях',
        ],
        StreetTypeInterface::STREET_TYPE_FORK => [
            'name' => 'Развилка',
            'shortName' => 'развилка',
            'nameUkr' => 'Розвилка',
            'shortNameUkr' => 'розвилка',
        ],
        StreetTypeInterface::STREET_TYPE_GROVE => [
            'name' => 'Роща',
            'shortName' => 'роща',
            'nameUkr' => 'Гай',
            'shortNameUkr' => 'гай',
        ],
        StreetTypeInterface::STREET_TYPE_SANATORIUM => [
            'name' => 'Санаторий',
            'shortName' => 'санат.',
            'nameUkr' => 'Санаторій',
            'shortNameUkr' => 'санат.',
        ],
        StreetTypeInterface::STREET_TYPE_SQUARE_1 => [
            'name' => 'Сквер',
            'shortName' => 'сквер',
            'nameUkr' => 'Сквер',
            'shortNameUkr' => 'сквер.',
        ],
        StreetTypeInterface::STREET_TYPE_STAGE_FARM => [
            'name' => 'Cовхоз',
            'shortName' => 'совх.',
            'nameUkr' => 'Радгосп',
            'shortNameUkr' => 'радг.',
        ],
        StreetTypeInterface::STREET_TYPE_STATION => [
            'name' => 'Станция',
            'shortName' => 'ст.',
            'nameUkr' => 'Станція',
            'shortNameUkr' => 'ст.',
        ],
        StreetTypeInterface::STREET_TYPE_HIGHWAY_1 => [
            'name' => 'Тракт',
            'shortName' => 'тракт',
            'nameUkr' => 'Тракт',
            'shortNameUkr' => 'тракт',
        ],
        StreetTypeInterface::STREET_TYPE_ROUTE => [
            'name' => 'Трасса',
            'shortName' => 'трасса',
            'nameUkr' => 'Траса',
            'shortNameUkr' => 'траса',
        ],
        StreetTypeInterface::STREET_TYPE_BLIND_ALLEY => [
            'name' => 'Тупик',
            'shortName' => 'тупик',
            'nameUkr' => 'Тупік',
            'shortNameUkr' => 'тупік',
        ],
        StreetTypeInterface::STREET_TYPE_DESCENT_1 => [
            'name' => 'Узвоз',
            'shortName' => 'узвоз',
            'nameUkr' => 'Узвіз',
            'shortNameUkr' => 'узвіз',
        ],
        StreetTypeInterface::STREET_TYPE_TRACT => [
            'name' => 'Урочище',
            'shortName' => 'урочище',
            'nameUkr' => 'Урочище',
            'shortNameUkr' => 'урочище',
        ],
        StreetTypeInterface::STREET_TYPE_FARM => [
            'name' => 'Хутор',
            'shortName' => 'хутор',
            'nameUkr' => 'Хутір',
            'shortNameUkr' => 'хутір',
        ],
        StreetTypeInterface::STREET_TYPE_ISLAND => [
            'name' => 'Остров',
            'shortName' => 'остров',
            'nameUkr' => 'Острів',
            'shortNameUkr' => 'острів',
        ],
    ];

    public function getStreetTypeName(): ?string
    {
        return self::$streetTypeNames[$this->streetType]['name'] ?? null;
    }

    public function getStreetTypeNameUkr(): ?string
    {
        return self::$streetTypeNames[$this->streetType]['nameUkr'] ?? null;
    }

    public function getStreetTypeShortName(): ?string
    {
        return self::$streetTypeNames[$this->streetType]['shortName'] ?? null;
    }

    public function getStreetTypeShortNameUkr(): ?string
    {
        return self::$streetTypeNames[$this->streetType]['shortNameUkr'] ?? null;
    }

    protected function findStreetTypeByName(string $name): ?int
    {
        foreach (self::$streetTypeNames as $type => $typeName) {
            $names = [
                mb_strtolower($typeName['name']),
                mb_strtolower($typeName['nameUkr']),
                mb_strtolower($typeName['shortName']),
                mb_strtolower($typeName['shortNameUkr']),
            ];

            if (in_array(mb_strtolower($name), $names, true)) {
                return $type;
            }
        }

        return null;
    }
}
