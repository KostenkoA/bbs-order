<?php

namespace App\Interfaces;

use Symfony\Component\Serializer\Annotation\Groups;

interface StreetTypeInterface
{
    public const STREET_TYPE_EMPTY = 0;

    public const STREET_TYPE_STREET = 1;

    public const STREET_TYPE_AVENUE = 2;

    public const STREET_TYPE_BOULEVARD = 3;

    public const STREET_TYPE_SQUARE = 4;

    public const STREET_TYPE_LANE = 5;

    public const STREET_TYPE_EMBANKMENT = 6;

    public const STREET_TYPE_DESCENT = 7;

    public const STREET_TYPE_HIGHWAY = 8;

    public const STREET_TYPE_MICRO_DISTRICT = 9;

    public const STREET_TYPE_HOUSING_AREA = 10;

    public const STREET_TYPE_QUARTER = 11;

    public const STREET_TYPE_ROAD = 12;

    public const STREET_TYPE_ALLEY = 13;

    public const STREET_TYPE_ENTRY = 14;

    public const STREET_TYPE_LINE = 15;

    public const STREET_TYPE_MAIDAN = 16;

    public const STREET_TYPE_PENSION = 17;

    public const STREET_TYPE_PARK = 18;

    public const STREET_TYPE_VILLAGE = 19;

    public const STREET_TYPE_PASSAGE = 20;

    public const STREET_TYPE_WAY = 21;

    public const STREET_TYPE_FORK = 22;

    public const STREET_TYPE_GROVE = 23;

    public const STREET_TYPE_SANATORIUM = 24;

    public const STREET_TYPE_SQUARE_1 = 25;

    public const STREET_TYPE_STAGE_FARM = 26;

    public const STREET_TYPE_STATION = 27;

    public const STREET_TYPE_HIGHWAY_1 = 28;

    public const STREET_TYPE_ROUTE = 29;

    public const STREET_TYPE_BLIND_ALLEY = 30;

    public const STREET_TYPE_DESCENT_1 = 31;

    public const STREET_TYPE_TRACT = 32;

    public const STREET_TYPE_FARM = 33;

    public const STREET_TYPE_ISLAND = 34;

    /**
     * @return int|null
     * @Groups({"info","admin.info","email"})
     */
    public function getStreetType(): ?int;


    /**
     * @return string|null
     * @Groups({"info","admin.info","email"})
     */
    public function getStreetTypeName(): ?string;

    /**
     * @return string|null
     * @Groups({"info","admin.info","email"})
     */
    public function getStreetTypeNameUkr(): ?string;

    /**
     * @return string|null
     * @Groups({"info","admin.info","email"})
     */
    public function getStreetTypeShortName(): ?string;

    /**
     * @return string|null
     * @Groups({"info","admin.info","email"})
     */
    public function getStreetTypeShortNameUkr(): ?string;
}
