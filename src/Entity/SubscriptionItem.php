<?php

namespace App\Entity;

use App\Component\Product\Response\Product;
use App\DTO\Subscription\SubscriptionItem as SubscriptionItemDto;
use App\Entity\Traits\CreatedUpdatedTrait;
use DateInterval;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="App\Repository\SubscriptionItemRepository")
 */
class SubscriptionItem
{
    use CreatedUpdatedTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @Groups({"all", "public.info", "admin.list", "admin.info"})
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Subscription", inversedBy="subscriptionItems")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $subscription;

    /**
     * @var string
     * @Groups({"all", "public.list", "public.info", "admin.list", "admin.info"})
     * @ORM\Column(type="string")
     */
    private $internalId;

    /**
     * @var string
     * @Groups({"all", "public.list", "public.info", "admin.list", "admin.info"})
     * @ORM\Column(type="string")
     */
    private $productId;

    /**
     * @var int
     * @Groups({"all", "public.list", "public.info", "admin.list", "admin.info"})
     * @ORM\Column(type="smallint")
     */
    private $quantity;

    /**
     * @var boolean
     * @Groups({"all", "public.list", "public.info", "admin.list", "admin.info"})
     * @ORM\Column(type="boolean")
     */
    private $isActive = true;

    /**
     * @var DateTime
     * @Groups({"all", "public.info", "public.list", "admin.list", "admin.info"})
     * @ORM\Column(type="date")
     */
    private $startDate;

    /**
     * @var DateTime|null
     * @Groups({"all", "public.list", "public.info", "admin.list", "admin.info"})
     * @ORM\Column(type="date", nullable=true)
     */
    private $skipDateFrom;

    /**
     * @var DateTime|null
     * @Groups({"all", "public.list", "public.info", "admin.list", "admin.info"})
     * @ORM\Column(type="date", nullable=true)
     */
    private $skipDateTo;

    /**
     * @var int
     * @Groups({"all", "public.info", "admin.list", "admin.info"})
     * @ORM\Column(type="integer")
     */
    private $intervalDays;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    /**
     * @return string
     */
    public function getInternalId(): string
    {
        return $this->internalId;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return DateTime
     */
    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    /**
     * @return DateTime|null
     */
    public function getSkipDateFrom(): ?DateTime
    {
        return $this->skipDateFrom;
    }

    /**
     * @return DateTime|null
     */
    public function getSkipDateTo(): ?DateTime
    {
        return $this->skipDateTo;
    }

    /**
     * @return int
     */
    public function getIntervalDays(): int
    {
        return $this->intervalDays;
    }

    public function isEnableForDate(DateTime $date): bool
    {
        if (!$this->getIsActive() || $date < $this->getStartDate()) {
            return false;
        }

        if (
            ($this->getSkipDateFrom() !== null || $this->getSkipDateTo() !== null) &&
            ($this->getSkipDateFrom() === null || $this->getSkipDateFrom() <= $date) &&
            ($this->getSkipDateTo() === null || $this->getSkipDateTo() >= $date)
        ) {
            return false;
        }

        $diff = $this->getStartDate()->diff($date, false);

        return (int)$diff->days % $this->getIntervalDays() === 0;
    }

    /**
     * @Groups({"all", "public.list", "public.info", "admin.list", "admin.info"})
     * @return DateTime|null
     * @throws Exception
     */
    public function getNextOrderDate(): ?DateTime
    {
        return $this->getNextEnableDate((new DateTime())->setTime(0, 0));
    }

    /**
     * @param DateTime $date
     * @param bool $after
     * @return DateTime
     * @throws Exception
     */
    public function getNextEnableDate(DateTime $date, bool $after = false): ?DateTime
    {
        if (!$this->getIsActive()) {
            return null;
        }

        if ($date < $this->getStartDate()) {
            if ($this->isEnableForDate($this->getStartDate())) {
                return $this->getStartDate();
            }

            $date = $this->getStartDate();
        }

        $diff = $date->diff($this->getStartDate(), false);
        $day = (int)$diff->days % $this->getIntervalDays();

        if ($day !== 0) {
            $date->add(new DateInterval(sprintf('P%dD', $this->getIntervalDays() - $day)));
        } elseif ($after) {
            $date->add(new DateInterval(sprintf('P%dD', $this->getIntervalDays())));
        }

        while (!$this->isEnableForDate($date)) {
            if ($this->getSkipDateTo() === null && $this->getSkipDateFrom() <= $date) {
                return null;
            }
            $date->add(new DateInterval(sprintf('P%dD', $this->getIntervalDays())));
        }

        return $date;
    }

    public function fillFromDto(SubscriptionItemDto $dto): void
    {
        $this->internalId = (string)$dto->internalId;
        $this->quantity = (integer)$dto->quantity;

        $this->intervalDays = $dto->intervalDays;
        $this->startDate = $dto->startDate ?? $this->startDate;
        $this->skipDateFrom = $dto->skipDateFrom;
        $this->skipDateTo = $dto->skipDateTo;
        $this->isActive = $dto->isActive !== null ? (bool)$dto->isActive : $this->isActive;
    }

    public function fillFromProductSearch(Product $product): void
    {
        $this->productId = (string)$product->productId;
        $this->internalId = (string)$product->intervalId;
    }
}
