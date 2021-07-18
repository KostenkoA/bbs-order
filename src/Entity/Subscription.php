<?php

namespace App\Entity;

use App\DTO\Subscription\Subscription as SubscriptionDTO;
use App\DTO\Subscription\SubscriptionItem as SubscriptionItemDTO;
use App\Entity\Traits\CreatedUpdatedTrait;
use App\Entity\Traits\StreetTypeTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="App\Repository\SubscriptionRepository")
 */
class Subscription
{
    use CreatedUpdatedTrait;
    use StreetTypeTrait;

    public const STATUS_NEW = 0;

    public const STATUS_APPROVED = 1;

    public const STATUS_NOT_APPROVED = 2;

    /**
     * @var integer|null
     * @Groups({"all", "public.list", "public.info", "admin.list", "admin.info"})
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @Groups({"all", "admin.list", "admin.info"})
     * @ORM\Column(type="string", length=25)
     */
    private $project;

    /**
     * @var string
     * @Groups({"all", "admin.list", "admin.info"})
     * @ORM\Column(type="string")
     */
    private $userRef;

    /**
     * @var boolean
     * @Groups({"all", "public.info", "admin.list", "admin.info"})
     * @ORM\Column(type="boolean")
     */
    private $isDefault;

    /**
     * @var integer
     * @Groups({"all", "public.list", "public.info", "admin.list", "admin.info"})
     * @ORM\Column(type="smallint", options={"default" : 0})
     */
    private $status = self::STATUS_NEW;

    /**
     * @var string
     * @Groups({"all", "public.list", "public.info", "admin.list", "admin.info"})
     * @ORM\Column(type="string", length=64)
     */
    private $firstName;

    /**
     * @var string
     * @Groups({"all", "public.list", "public.info", "admin.list", "admin.info"})
     * @ORM\Column(type="string", length=64)
     */
    private $lastName;

    /**
     * @var string|null
     * @Groups({"all", "public.list", "public.info"})
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $middleName;

    /**
     * @var string|null
     * @Groups({"all", "public.list", "public.info", "admin.list", "admin.info"})
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $phone;

    /**
     * @var string|null
     * @Groups({"all", "public.list", "public.info", "admin.info"})
     * @ORM\Column(type="string", nullable=true)
     */
    private $email;

    /**
     * @var integer
     * @Groups({"all", "public.info", "admin.info"})
     * @ORM\Column(type="smallint")
     */
    private $deliveryType;

    /**
     * @var string|null
     * @Groups({"all", "public.info", "admin.info"})
     * @ORM\Column(type="string", nullable=true)
     */
    private $deliveryBranch;

    /**
     * @var string|null
     * @Groups({"all", "public.info", "admin.info"})
     * @ORM\Column(type="string", nullable=true)
     */
    private $deliveryShop;

    /**
     * @var integer|null
     * @Groups({"all", "public.info", "admin.info"})
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $deliveryCarrier;

    /**
     * @var integer
     * @Groups({"all", "public.info", "admin.info"})
     * @ORM\Column(type="smallint")
     */
    private $paymentType;

    /**
     * @var string|null
     * @Groups({"all", "public.info", "admin.info"})
     * @ORM\Column(type="string", nullable=true)
     */
    private $region;

    /**
     * @var string|null
     * @Groups({"all", "public.info", "admin.info"})
     * @ORM\Column(type="string", nullable=true)
     */
    private $district;

    /**
     * @var string|null
     * @Groups({"all", "public.info", "admin.info"})
     * @ORM\Column(type="string", nullable=true)
     */
    private $city;

    /**
     * @var int|null
     * @Groups({"all", "public.info", "admin.info"})
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $streetType;

    /**
     * @var string|null
     * @Groups({"all", "public.info", "admin.info"})
     * @ORM\Column(type="string", nullable=true)
     */
    private $street;

    /**
     * @var string|null
     * @Groups({"all", "public.info", "admin.info"})
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $building;

    /**
     * @var string|null
     * @Groups({"all", "public.info", "admin.info"})
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $apartment;

    /**
     * @var boolean
     * @Groups({"all", "public.info", "admin.info"})
     * @ORM\Column(type="boolean")
     */
    private $isActive = true;

    /**
     * @var Order[]|Collection
     * @Groups({"all"})
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="subscription")
     */
    private $orderList;

    /**
     * @var SubscriptionItem[]|Collection
     * @Groups({"all", "public.list", "public.info", "admin.list", "admin.info"})
     * @ORM\OneToMany(targetEntity="SubscriptionItem", mappedBy="subscription", cascade={"persist"}, orphanRemoval=true)
     */
    private $subscriptionItems;

    /**
     * @var Card|null
     * @Groups({"all", "public.info", "admin.info"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Card")
     * @ORM\JoinColumn(onDelete="SET NULL", nullable=true)
     */
    private $card;

    /**
     * @var string|null
     * @Groups({"all", "public.info", "admin.info"})
     * @ORM\Column(type="string", nullable=true)
     */
    private $cityRef;

    /**
     * @var string|null
     * @Groups({"all", "public.info", "admin.info"})
     * @ORM\Column(type="string", nullable=true)
     */
    private $deliveryBranchRef;

    /**
     * @var string|null
     * @Groups({"all", "public.info", "admin.info"})
     * @ORM\Column(type="string", nullable=true)
     */
    private $streetRef;

    public function __construct(string $project, string $userRef, bool $isDefault = true)
    {
        $this->project = $project;
        $this->userRef = $userRef;
        $this->isDefault = $isDefault;

        $this->orderList = new ArrayCollection();
        $this->subscriptionItems = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getProject(): string
    {
        return $this->project;
    }

    /**
     * @return string
     */
    public function getUserRef(): string
    {
        return $this->userRef;
    }

    /**
     * @return bool
     */
    public function getIsDefault(): bool
    {
        return $this->isDefault;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string|null
     */
    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getDeliveryType(): int
    {
        return $this->deliveryType;
    }

    /**
     * @return string|null
     */
    public function getDeliveryBranch(): ?string
    {
        return $this->deliveryBranch;
    }

    /**
     * @return string|null
     */
    public function getDeliveryShop(): ?string
    {
        return $this->deliveryShop;
    }

    /**
     * @return int|null
     */
    public function getDeliveryCarrier(): ?int
    {
        return $this->deliveryCarrier;
    }

    /**
     * @return int
     */
    public function getPaymentType(): int
    {
        return $this->paymentType;
    }

    /**
     * @return string|null
     */
    public function getRegion(): ?string
    {
        return $this->region;
    }

    /**
     * @return string|null
     */
    public function getDistrict(): ?string
    {
        return $this->district;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @return int|null
     */
    public function getStreetType(): ?int
    {
        return $this->streetType;
    }

    /**
     * @return string|null
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @return string|null
     */
    public function getBuilding(): ?string
    {
        return $this->building;
    }

    /**
     * @return string|null
     */
    public function getApartment(): ?string
    {
        return $this->apartment;
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return Order[]|Collection
     */
    public function getOrderList()
    {
        return $this->orderList;
    }

    /**
     * @return Collection|SubscriptionItem[]
     */
    public function getSubscriptionItems()
    {
        return $this->subscriptionItems;
    }

    /**
     * @return Card|null
     */
    public function getCard(): ?Card
    {
        return $this->card;
    }

    /**
     * @return string|null
     */
    public function getCityRef(): ?string
    {
        return $this->cityRef;
    }

    /**
     * @return string|null
     */
    public function getDeliveryBranchRef(): ?string
    {
        return $this->deliveryBranchRef;
    }

    /**
     * @return string|null
     */
    public function getStreetRef(): ?string
    {
        return $this->streetRef;
    }

    public function isEnableForOrder(): bool
    {
        return $this->getStatus() === self::STATUS_APPROVED;
    }

    /**
     * @Groups({"all", "public.list", "public.info", "admin.list", "admin.info"})
     * @return DateTime|null
     * @throws Exception
     */
    public function getNextOrderDate(): ?DateTime
    {
        $dates = [];
        foreach ($this->getSubscriptionItems() as $item) {
            if ($nextDate = $item->getNextOrderDate()) {
                $dates[] = $nextDate;
            }
        }

        return min($dates);
    }

    /**
     * @param DateTime $date
     * @param bool $after
     * @return DateTime|null
     * @throws Exception
     */
    public function getNextEnableDate(DateTime $date, bool $after = false): ?DateTime
    {
        $dates = [];
        foreach ($this->getSubscriptionItems() as $item) {
            if ($nexDate = $item->getNextEnableDate($date, $after)) {
                $dates[] = $nexDate;
            }
        }

        return min($dates);
    }


    /**
     * @Groups({"all", "public.list", "public.info", "admin.list", "admin.info"})
     * @return DateTime
     */
    public function getStartDate(): DateTime
    {
        $dates = [];
        foreach ($this->getSubscriptionItems() as $item) {
            $dates[] = $item->getStartDate();
        }

        return min($dates);
    }

    public function getEnableItemsForDate(DateTime $date): Collection
    {
        return $this->subscriptionItems->filter(
            static function (SubscriptionItem $item) use ($date) {
                return $item->isEnableForDate($date);
            }
        );
    }

    public function isEnableForDate(DateTime $date): bool
    {
        return (bool)($this->getEnableItemsForDate($date)->count());
    }

    public function updateByCard(Card $card): void
    {
        $this->card = $card;
        $this->status = self::STATUS_NOT_APPROVED;
        $this->checkStatus();
    }

    public function checkStatus(): void
    {
        if ($this->getPaymentType() === PaymentTypeInterface::PAYMENT_CARD &&
            ($card = $this->getCard()) &&
            in_array($this->status, [self::STATUS_NEW, self::STATUS_NOT_APPROVED], true)
        ) {
            $this->status = $card->getIsVerified() ? self::STATUS_APPROVED : self::STATUS_NOT_APPROVED;
        } elseif (
            $this->getPaymentType() === PaymentTypeInterface::PAYMENT_CARD_SHOP &&
            $this->getDeliveryType() === DeliveryTypeInterface::DELIVERY_SHOP
        ) {
            $this->status = self::STATUS_APPROVED;
        } elseif ($this->getPaymentType() === PaymentTypeInterface::PAYMENT_CASH) {
            $this->status;
        }
    }

    public function updateFromDto(SubscriptionDTO $dto): void
    {
        if (!$this->isDefault) {
            $this->isActive = $dto->isActive !== null ? (bool)$dto->isActive : $this->isActive;
        }

        $this->firstName = $dto->firstName;
        $this->lastName = $dto->lastName;
        $this->middleName = $dto->middleName;
        $this->phone = $dto->phone;
        $this->email = $dto->email;

        $this->deliveryType = $dto->deliveryType;

        $this->deliveryCarrier = $dto->deliveryCarrier;

        $this->region = $dto->region;
        $this->district = $dto->district;
        $this->city = $dto->city;
        $this->cityRef = $dto->cityRef;
        $this->streetType = $dto->streetType ?? $this->findStreetTypeByName($order->streetTypeName ?? '');
        $this->street = $dto->street;
        $this->streetRef = $dto->streetRef;
        $this->building = $dto->building;
        $this->apartment = $dto->apartment;
        $this->deliveryBranch = $dto->deliveryBranch;
        $this->deliveryShop = $dto->deliveryShop;
        $this->deliveryBranchRef = $dto->deliveryBranchRef;

        if ($this->paymentType !== $dto->paymentType) {
            $this->status = self::STATUS_NEW;
        }

        $this->paymentType = $dto->paymentType;

        if (!empty($dto->items)) {
            $this->subscriptionItems->clear();

            foreach ($dto->items as $item) {
                $subscriptionItem = new SubscriptionItem($this);
                $subscriptionItem->fillFromDto($item);

                $this->subscriptionItems->add($subscriptionItem);
            }
        }

        $this->checkStatus();
    }

    /**
     * @param SubscriptionItemDTO $dto
     */
    public function updateOrAddItem(SubscriptionItemDTO $dto): void
    {
        $subscriptionItem = $this->findItemByInternalId($dto->internalId) ?? new SubscriptionItem($this);
        $subscriptionItem->fillFromDto($dto);

        $index = $this->subscriptionItems->indexOf($subscriptionItem);

        if ($index !== false) {
            $this->subscriptionItems->set($index, $subscriptionItem);
        } else {
            $this->subscriptionItems->add($subscriptionItem);
        }
    }

    protected function findItemByInternalId(string $internalId): ?SubscriptionItem
    {
        foreach ($this->subscriptionItems as $subscriptionItem) {
            if ($subscriptionItem->getInternalId() === $internalId) {
                return $subscriptionItem;
            }
        }

        return null;
    }
}
