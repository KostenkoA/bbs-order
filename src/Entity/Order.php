<?php

namespace App\Entity;

use App\Component\Delivery\Response\Settlement;
use App\Component\Delivery\Response\Street;
use App\Component\Delivery\Response\Warehouse;
use App\DTO\BasketChecked\BasketCheckedGiftList;
use App\DTO\BasketChecked\BasketCheckedGiftNomenclature;
use App\DTO\NewOrder;
use App\Entity\Traits\StreetTypeTrait;
use App\Interfaces\StreetTypeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Traits\CreatedUpdatedTrait;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Table(name="`order`",indexes={@ORM\Index(name="created_at_idx", columns={"created_at"})})
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Order implements
    StatusInterface,
    DeliveryCarrierInterface,
    DeliveryTypeInterface,
    PaymentTypeInterface,
    StreetTypeInterface
{
    use CreatedUpdatedTrait;
    use StreetTypeTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"admin.list","admin.info"})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string|null
     * @Serializer\Groups({"admin.list","admin.info"})
     * @ORM\Column(type="string", length=36, nullable=true)
     */
    private $ref;

    /**
     * @var string
     * @Serializer\Groups({"created","info","admin.list","admin.info","public.checkout","public.list","email"})
     * @ORM\Column(type="string", length=36, unique=true)
     */
    private $hash;

    /**
     * @var string
     * @Serializer\Groups({"created","info","admin.list","admin.info","public.checkout","public.list","email","sms"})
     * @ORM\Column(type="string", length=12, unique=true)
     */
    private $number;

    /**
     * @var string|null
     * @Serializer\Groups({"admin.list","admin.info"})
     * @ORM\Column(type="string", nullable=true)
     */
    private $userRef;

    /**
     * @var string
     * @Serializer\Groups({"info","admin.list","admin.info","email"})
     * @ORM\Column(type="string", nullable=true, length=64)
     */
    private $firstName;

    /**
     * @var string
     * @Serializer\Groups({"info","admin.list","admin.info","email"})
     * @ORM\Column(type="string", nullable=true, length=64)
     */
    private $lastName;

    /**
     * @var string|null
     * @Serializer\Groups({"info","admin.list","admin.info","email"})
     * @ORM\Column(type="string", nullable=true, length=64, nullable=true)
     */
    private $middleName;

    /**
     * @var string|null
     * @Serializer\Groups({"info", "admin.list","admin.info","email"})
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $phone;

    /**
     * @var string|null
     * @Serializer\Groups({"info", "admin.list","admin.info","email"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var integer
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="smallint")
     */
    private $deliveryType;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $deliveryBranchRef;

    /**
     * @var string|null
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $deliveryBranch;

    /**
     * @var string|null
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $deliveryBranchUkr;

    /**
     * @var string|null
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $deliveryShop;

    /**
     * @var integer|null
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $deliveryCarrier;

    /**
     * @var integer
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="smallint")
     */
    private $paymentType;

    /**
     * @var integer
     * @Serializer\Groups({"created","info","admin.list","admin.info","public.list"})
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @var integer
     * @Serializer\Groups({"info","admin.info"})
     * @ORM\Column(type="smallint")
     */
    private $deliveryStatus = DeliveryStatusInterface::STATUS_NEW;

    /**
     * @var integer
     * @Serializer\Groups({"info","admin.info"})
     * @ORM\Column(type="smallint")
     */
    private $paymentStatus = PaymentStatusInterface::STATUS_NEW;

    /**
     * @var string|null
     * @Serializer\Groups({"info","admin.info"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $region;

    /**
     * @var string|null
     * @Serializer\Groups({"info","admin.info"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $regionUkr;

    /**
     * @var string|null
     * @Serializer\Groups({"info","admin.info"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $district;

    /**
     * @var string|null
     * @Serializer\Groups({"info","admin.info"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $districtUkr;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $cityRef;

    /**
     * @var string|null
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var string|null
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cityUkr;

    /**
     * @var int|null
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cityType;

    /**
     * @var string|null
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cityTypeUkr;

    /**
     * @var string|null
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $streetType;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $streetRef;

    /**
     * @var string|null
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $street;

    /**
     * @var string|null
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $streetUkr;

    /**
     * @var string|null
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $building;

    /**
     * @var string|null
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $apartment;

    /**
     * @var string|null
     * @Serializer\Groups({"info","admin.info","email","admin.list"})
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    private $comment;

    /**
     * @var float
     * @Serializer\Groups({"info","admin.list","admin.info","email"})
     * @ORM\Column(type="decimal", scale=0, options={"default" : 0})
     */
    private $price = 0;

    /**
     * @var float
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="decimal", scale=0, options={"default" : 0})
     */
    private $deliveryPrice = 0;

    /**
     * @var float
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="decimal", scale=0, options={"default" : 0})
     */
    private $bonusDiscountAmount = 0;

    /**
     * @var float
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="decimal", scale=0, options={"default" : 0})
     */
    private $discountAmount = 0;

    /**
     * @var int
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="integer", scale=0, options={"default" : 0})
     */
    private $usedBonuses = 0;

    /**
     * @var float
     * @Serializer\Groups({"created","info","admin.list","admin.info","public.list","email"})
     * @ORM\Column(type="decimal", scale=0, options={"default" : 0})
     */
    private $cost = 0;

    /**
     * @var ArrayCollection|OrderItem[]
     * @Serializer\Groups({"public.list","info","admin.info","email"})
     * @ORM\OneToMany(targetEntity="App\Entity\OrderItem", mappedBy="order", fetch="EAGER", cascade={"persist"})
     */
    private $orderItems;

    /**
     * @Serializer\Groups({"admin.info"})
     * @ORM\OneToMany(targetEntity="App\Entity\OrderStatusHistory", mappedBy="order", fetch="LAZY")
     */
    private $statusHistory;

    /**
     * @Serializer\Groups({"admin.info","email"})
     * @ORM\Column(type="boolean")
     */
    private $callBack = true;

    /**
     * @var bool|null
     * @Serializer\Groups({"admin.info"})
     * @ORM\Column(type="boolean", options={"default" : false})
     */
    private $fromAnon = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Payment", mappedBy="order")
     */
    private $payment;

    /**
     * @var string
     * @Serializer\Groups({"admin.info"})
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $projectName;

    /**
     * @var string[]|null
     * @Serializer\Groups({"admin.info","info", "email"})
     * @ORM\Column(type="json", options={}, nullable=true)
     */
    private $certificates;

    /**
     * @var Subscription|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Subscription", inversedBy="orders")
     * @ORM\JoinColumn(onDelete="SET NULL", nullable=true)
     */
    private $subscription;

    /**
     * Order constructor.
     */
    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
        $this->status = self::STATUS_NEW;
        $this->statusHistory = new ArrayCollection();
        $this->payment = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getRef(): ?string
    {
        return $this->ref;
    }

    /**
     * @return string|null
     */
    public function getHash(): ?string
    {
        return $this->hash;
    }

    /**
     * @return string|null
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * @return string|null
     */
    public function getUserRef(): ?string
    {
        return $this->userRef;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
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
     * @return int|null
     */
    public function getDeliveryType(): ?int
    {
        return $this->deliveryType;
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
    public function getDeliveryBranch(): ?string
    {
        return $this->deliveryBranch;
    }

    /**
     * @return string|null
     */
    public function getDeliveryBranchUkr(): ?string
    {
        return $this->deliveryBranchUkr;
    }

    /**
     * @return string
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
     * @return int|null
     */
    public function getPaymentType(): ?int
    {
        return $this->paymentType;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getDeliveryStatus(): int
    {
        return $this->deliveryStatus;
    }

    /**
     * @return int
     */
    public function getPaymentStatus(): int
    {
        return $this->paymentStatus;
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
    public function getRegionUkr(): ?string
    {
        return $this->regionUkr;
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
    public function getDistrictUkr(): ?string
    {
        return $this->districtUkr;
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
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @return string|null
     */
    public function getCityUkr(): ?string
    {
        return $this->cityUkr;
    }


    /**
     * @return string|null
     */
    public function getCityType(): ?string
    {
        return $this->cityType;
    }

    /**
     * @return string|null
     */
    public function getCityTypeUkr(): ?string
    {
        return $this->cityTypeUkr;
    }

    /**
     * @return string|null
     */
    public function getStreetRef(): ?string
    {
        return $this->streetRef;
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
    public function getStreetUkr(): ?string
    {
        return $this->streetUkr;
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
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return float
     */
    public function getDeliveryPrice(): float
    {
        return $this->deliveryPrice;
    }

    /**
     * @return float
     */
    public function getBonusDiscountAmount(): float
    {
        return $this->bonusDiscountAmount;
    }

    /**
     * @return float
     */
    public function getDiscountAmount(): float
    {
        return $this->discountAmount;
    }

    /**
     * @return int
     */
    public function getUsedBonuses(): int
    {
        return $this->usedBonuses;
    }

    /**
     * @return float
     */
    public function getCost(): float
    {
        return $this->cost;
    }

    /**
     * @return bool|null
     */
    public function getCallBack(): ?bool
    {
        return $this->callBack;
    }

    /**
     * @return bool|null
     */
    public function getFromAnon(): ?bool
    {
        return $this->fromAnon;
    }

    /**
     * @Serializer\MaxDepth(1)
     * @return Collection|OrderItem[]
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    /**
     * @param OrderItem $orderItem
     * @return Order
     */
    public function addOrderItem(OrderItem $orderItem): self
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems[] = $orderItem;
            $orderItem->setOrder($this);
        }

        return $this;
    }

    /**
     * @param OrderItem $orderItem
     * @return Order
     */
    public function removeOrderItem(OrderItem $orderItem): self
    {
        if ($this->orderItems->contains($orderItem)) {
            $this->orderItems->removeElement($orderItem);
            // set the owning side to null (unless already changed)
            if ($orderItem->getOrder() === $this) {
                $orderItem->setOrder(null);
            }
        }

        return $this;
    }

    /**
     * @Serializer\MaxDepth(1)
     * @return Collection|OrderStatusHistory[]
     */
    public function getStatusHistory(): Collection
    {
        return $this->statusHistory;
    }

    /**
     * @param OrderStatusHistory $statusHistory
     * @return Order
     */
    public function addStatusHistory(OrderStatusHistory $statusHistory): self
    {
        if (!$this->statusHistory->contains($statusHistory)) {
            $this->statusHistory[] = $statusHistory;
            $statusHistory->setOrder($this);
        }

        return $this;
    }

    /**
     * @param OrderStatusHistory $statusHistory
     * @return Order
     */
    public function removeStatusHistory(OrderStatusHistory $statusHistory): self
    {
        if ($this->statusHistory->contains($statusHistory)) {
            $this->statusHistory->removeElement($statusHistory);
            // set the owning side to null (unless already changed)
            if ($statusHistory->getOrder() === $this) {
                $statusHistory->setOrder(null);
            }
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStreetType(): ?int
    {
        return $this->streetType;
    }

    /**
     * @return float
     */
    public function getCostWithoutDelivery(): float
    {
        return $this->price - $this->bonusDiscountAmount - $this->discountAmount;
    }

    /**
     * @return Collection|Payment[]
     */
    public function getPayment(): Collection
    {
        return $this->payment;
    }

    /**
     * @return string
     */
    public function getProjectName(): string
    {
        return $this->projectName;
    }

    /**
     * @return string[]|null
     */
    public function getCertificates(): ?array
    {
        return $this->certificates;
    }

    /**
     * @return Subscription|null
     */
    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    /**
     * @ORM\PrePersist()
     * @return Order
     * @throws Exception
     */
    public function generateHash(): self
    {
        $this->hash = Uuid::uuid4()->toString();

        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @return Order
     * @throws Exception
     */
    public function generateNumber(): self
    {
        //will be generated in trigger "BEFORE INSERT"
        $this->number = '';

        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @return Order
     * @throws Exception
     */
    public function calculatePrice(): self
    {
        $orderItems = $this->getOrderItems();

        $price = 0;

        /** @var OrderItem $item */
        foreach ($orderItems->getIterator() as $item) {
            if (!$item->getIsGift()) {
                $price += (float)$item->getTotalPrice();
            }
        }

        $this->price = $price;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @return Order
     */
    public function calculateCost(): self
    {
        $costWithDiscount = $this->price - $this->bonusDiscountAmount - $this->discountAmount;
        $costWithDiscount = $costWithDiscount > 0 ? $costWithDiscount : 0;

        $this->cost = $costWithDiscount + $this->deliveryPrice;

        return $this;
    }

    public function fillFromNewOrder(NewOrder $order, bool $fromAnon = null): void
    {
        $this->fromAnon = (bool) $fromAnon;

        $this->userRef = $order->userRef;
        $this->firstName = $order->firstName;
        $this->lastName = $order->lastName;
        $this->middleName = $order->middleName;
        $this->phone = $order->phone;
        $this->email = $order->email;
        $this->paymentType = $order->paymentType;
        $this->comment = $order->comment;
        $this->callBack = (bool)$order->callBack;
        $this->certificates = $order->certificates;

        //TODO: после дороботки \App\Component\Delivery перенести в updateDelivery
        $this->deliveryType = $order->deliveryType;
        $this->deliveryBranchRef = $order->deliveryBranchRef;
        $this->deliveryBranch = $order->deliveryBranch;
        $this->deliveryShop = $order->deliveryShop;
        $this->deliveryCarrier = $order->deliveryCarrier;
        $this->region = $order->region;
        $this->district = $order->district;
        $this->cityRef = $order->cityRef;
        $this->city = $order->city;
        $this->streetType = $order->streetType ?? $this->findStreetTypeByName($order->streetTypeName ?? '');
        $this->streetRef = $order->streetRef;
        $this->street = $order->street;
        $this->building = $order->building;
        $this->apartment = $order->apartment;


        if (!empty($order->orderItems)) {
            foreach ($order->orderItems as $item) {
                $orderItem = new OrderItem((string)$item->internalId, (int)$item->quantity, $this);
                $this->addOrderItem($orderItem);
            }
        }

        $this->projectName = $order->project;
    }

    public function addGiftOrderItem(BasketCheckedGiftNomenclature $dto, BasketCheckedGiftList $giftList): void
    {
        $orderItem = new OrderItem($dto->getNomenclatureId(), null, $this);
        $orderItem->fillByGift($dto, $giftList);
        $this->addOrderItem($orderItem);
    }

    public function clearEmptyOrderItems(): void
    {
        foreach ($this->orderItems as $orderItem) {
            if ($orderItem->getQuantity() === 0) {
                $this->removeOrderItem($orderItem);
            }
        }
    }

    public function calculateDiscountAmount(): void
    {
        $discountAmount = 0;
        /** @var OrderItem $orderItem */
        foreach ($this->orderItems as $orderItem) {
            $discountAmount += $orderItem->getDiscountAmount();
        }

        $this->discountAmount = $discountAmount;
    }

    /**
     * @param float $deliveryPrice
     */
    public function updateDeliveryPrice(float $deliveryPrice): void
    {
        $this->deliveryPrice = $deliveryPrice;
    }

    /**
     * @param string|null $ref
     */
    public function updateRef(?string $ref): void
    {
        $this->ref = $ref;
    }

    /**
     * @param int $status
     */
    public function updateStatus(int $status): void
    {
        $this->status = $status;
    }

    public function updatePaymentStatus(): void
    {
        $paymentStatus = null;
        /** @var Payment $payment */
        foreach ($this->getPayment()->getValues() as $payment) {
            if ($payment->getStatus() === PaymentStatusInterface::STATUS_APPROVED) {
                $paymentStatus = $payment->getStatus();
                break;
            }
            if ($paymentStatus !== PaymentStatusInterface::STATUS_PROCESSING) {
                $paymentStatus = $payment->getStatus();
            }
        }

        if ($paymentStatus !== null) {
            $this->paymentStatus = $paymentStatus;
        }
    }

    /**
     * @param float $bonusDiscountAmount
     * @param int $usedBonuses
     */
    public function updateUsedBonuses(float $bonusDiscountAmount, int $usedBonuses): void
    {
        $this->bonusDiscountAmount = $bonusDiscountAmount;
        $this->usedBonuses = $usedBonuses;
    }

    /**
     * @param Settlement|null $dto
     * @param Street|null $streetDto
     * @param Warehouse|null $branchDto
     */
    public function updateFromDelivery(?Settlement $dto, ?Street $streetDto = null, ?Warehouse $branchDto = null): void
    {
        $this->region = $dto && $dto->regionName ? $dto->regionName : $this->regionUkr;
        $this->regionUkr = $dto && $dto->regionNameUkr ? $dto->regionNameUkr : $this->regionUkr;

        $this->district = $dto && $dto->districtName ? $dto->districtName : $this->district;
        $this->districtUkr = $dto && $dto->districtNameUkr ? $dto->districtNameUkr : $this->districtUkr;

        $this->city = $dto && $dto->name ? $dto->name : $this->city;
        $this->cityUkr = $dto && $dto->nameUkr ? $dto->nameUkr : $this->cityUkr;

        $settlementType = $dto && $dto->settlementType ? explode('|', $dto->settlementType) : [];
        $this->cityType = $settlementType[0] ?? $this->cityType;
        $this->cityTypeUkr = $settlementType[1] ?? $this->cityTypeUkr;

        $this->street = $streetDto && $streetDto->name ? $streetDto->name : $this->street;
        $this->streetUkr = $streetDto && $streetDto->nameUkr ? $streetDto->nameUkr : $this->streetUkr;

        $this->deliveryBranch = $branchDto && $branchDto->name ? $branchDto->name : $this->deliveryBranch;
        $this->deliveryBranchUkr = $branchDto && $branchDto->nameUkr ? $branchDto->nameUkr : $this->deliveryBranchUkr;
    }

    public function setSubscription(?Subscription $subscription): void
    {
        $this->subscription = $subscription;
    }
}
