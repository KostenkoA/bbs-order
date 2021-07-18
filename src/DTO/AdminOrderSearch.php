<?php

namespace App\DTO;

use Symfony\Component\Serializer\Annotation as Serializer;
use App\Entity\StatusInterface;
use DateTime;

class AdminOrderSearch extends OrderSearch
{
    /**
     * @var string
     * @Serializer\Groups({"admin"})
     */
    public $ref;

    /**
     * @var string
     * @Serializer\Groups({"admin"})
     */
    public $firstName;

    /**
     * @var string
     * @Serializer\Groups({"admin"})
     */
    public $lastName;

    /**
     * @var string
     * @Serializer\Groups({"admin"})
     */
    public $number;

    /**
     * @var array
     * @Serializer\Groups({"admin"})
     */
    public $userRef;

    /**
     * @var array
     * @Serializer\Groups({"admin"})
     */
    public $status;

    /**
     * @var string
     * @Serializer\Groups({"admin"})
     */
    public $phone;

    /**
     * @var string
     * @Serializer\Groups({"admin"})
     */
    public $email;

    /**
     * @var string
     * @Serializer\Groups({"admin"})
     */
    public $projectName;

    /**
     * @var DateTime|null
     */
    public $createDate;

    /**
     * @var DateTime|null
     */
    public $createdAtFrom;

    /**
     * @var DateTime|null
     */
    public $createdAtTo;

    /**
     * @var Sort[]|null
     * @Serializer\Groups({"admin"})
     */
    public $sort;

    /**
     * @var Pagination|null
     * @Serializer\Groups({"admin"})
     */
    public $pagination;


    public static function availableStatuses(): array
    {
        return [
            StatusInterface::STATUS_NEW => StatusInterface::STATUS_NEW,
            StatusInterface::STATUS_REGISTERED => StatusInterface::STATUS_REGISTERED,
            StatusInterface::STATUS_IN_PROGRESS => StatusInterface::STATUS_IN_PROGRESS,
            StatusInterface::STATUS_TO_DELIVERY => StatusInterface::STATUS_TO_DELIVERY,
            StatusInterface::STATUS_TRANSIT => StatusInterface::STATUS_TRANSIT,
            StatusInterface::STATUS_CANCELED => StatusInterface::STATUS_CANCELED,
            StatusInterface::STATUS_COMPLETED => StatusInterface::STATUS_COMPLETED,
            StatusInterface::STATUS_ERROR_FROM_1C => StatusInterface::STATUS_ERROR_FROM_1C,
            StatusInterface::STATUS_PARTIAL_COLLECTED => StatusInterface::STATUS_PARTIAL_COLLECTED,
            StatusInterface::STATUS_COLLECTED => StatusInterface::STATUS_COLLECTED,
        ];
    }
}
