<?php

namespace App\Entity;

use App\Entity\Traits\CreatedUpdatedTrait;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;
use DateTime;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Payment
{
    use CreatedUpdatedTrait;

    public const TYPE_ORDER_PAYMENT = 0;

    public const TYPE_CARD_VERIFICATION = 1;

    public const CARD_VERIFICATION_COST = 1;

    /**
     * @var integer
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @Serializer\Groups({"public.checkout"})
     * @ORM\Column(type="string", length=23)
     */
    private $number;

    /**
     * @var string
     * @Serializer\Groups({"public.checkout"})
     * @ORM\Column(type="string", length=36)
     */
    private $hash;

    /**
     * @var Order|null
     * @Serializer\Groups({"public.checkout"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="payment")
     * @ORM\JoinColumn(nullable=true)
     */
    private $order;

    /**
     * @var Card|null
     * @Serializer\Groups({"public.checkout"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Card", inversedBy="subscribtionPayment")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $card;

    /**
     * @var integer
     * @Serializer\Groups({"public.checkout"})
     * @ORM\Column(type="smallint")
     */
    private $method;

    /**
     * @var integer
     * @Serializer\Groups({"public.checkout"})
     * @ORM\Column(type="smallint")
     */
    private $status = PaymentStatusInterface::STATUS_NEW;

    /**
     * @var integer
     * @Serializer\Groups({"public.checkout"})
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $type;

    /**
     * @var integer|null
     * @Serializer\Groups({"public.checkout"})
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $refundStatus;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=10, scale=0)
     */
    private $cost;

    /**
     * @var string
     * @Serializer\Groups({"public.checkout"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $response = [];

    /**
     * Payment constructor.
     * @param Order|null $order
     * @param Card|null $card
     * @param int $type
     * @param int $method
     * @param float $cost
     */
    public function __construct(?Order $order, ?Card $card, int $type, int $method, float $cost)
    {
        if ($order) {
            $this->order = $order;
        }

        if ($card) {
            $this->card = $card;
        }

        $this->type = $type;
        $this->method = $method;
        $this->cost = $cost;
    }

    /**
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return Order|null
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @return Card|null
     */
    public function getCard(): ?Card
    {
        return $this->card;
    }

    /**
     * @return integer
     */
    public function getMethod(): int
    {
        return $this->method;
    }

    /**
     * @return integer
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return float
     */
    public function getCost(): float
    {
        return $this->cost;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $hash
     *
     * @return $this
     */
    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @param int $method
     *
     * @return $this
     */
    public function setMethod(int $method): self
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param float $cost
     *
     * @return $this
     */
    public function setCost(float $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getRefundStatus(): ?int
    {
        return $this->refundStatus;
    }

    public function getResponse(): ?array
    {
        return $this->response;
    }


    public function isTypeCardVerification(): bool
    {
        return $this->type === self::TYPE_CARD_VERIFICATION;
    }

    public function isForCardToken(): bool
    {
        return ($this->order && $this->order->getSubscription()) || $this->isTypeCardVerification();
    }

    /**
     * @ORM\PrePersist()
     * @return Payment
     * @throws Exception
     */
    public function generateHash(): self
    {
        $this->hash = Uuid::uuid4()->toString();

        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @throws Exception
     */
    public function generateNumber(): void
    {
        if ($order = $this->getOrder()) {
            $this->number = sprintf('%s#%s', $order->getNumber(), (new DateTime())->getTimestamp());
        } elseif ($card = $this->getCard()) {
            $this->number = sprintf('%s#%s', $card->getId(), (new DateTime())->getTimestamp());
        }
    }

    public function setStatusCreated(): self
    {
        $this->setStatus(PaymentStatusInterface::STATUS_CREATED);

        return $this;
    }


    public function setResponse(?array $response): self
    {
        $this->response = $response;

        return $this;
    }
}
