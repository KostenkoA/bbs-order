<?php

namespace App\DTO\Subscription;

use App\DTO\Pagination;
use App\DTO\Project;
use DateTime;

class AdminSubscriptionSearch extends Project
{
    /** @var string|null */
    public $userRef;

    /** @var string|null */
    public $firstName;

    /** @var string|null */
    public $lastName;

    /** @var string[]|null */
    public $internalId = [];

    /** @var string|null */
    public $productId;

    /** @var string|null */
    public $phone;

    /** @var int|null */
    public $status;

    /** @var int|null */
    public $isActive;

    /** @var DateTime|null */
    public $dateFrom;

    /** @var DateTime|null */
    public $dateTo;

    /** @var Pagination|null */
    public $pagination;
}
