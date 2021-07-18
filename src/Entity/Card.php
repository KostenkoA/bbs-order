<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\CreatedUpdatedTrait;
use App\Component\Payment\Model\Card as CardDto;
use Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CardRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Card implements PaymentMethodInterface
{
    use CreatedUpdatedTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Groups({"created", "public.checkout", "public.info"})
     * @ORM\Column(type="string", length=36, unique=true)
     */
    private $hash;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $userRef;

    /**
     * @var string
     * @Groups({"created", "public.checkout"})
     * @ORM\Column(type="string")
     */
    private $project;

    /**
     * @var integer
     * @Groups({"public.checkout", "public.info"})
     * @ORM\Column(type="smallint")
     */
    private $method;

    /**
     * @var bool
     * @Groups({"created", "public.checkout", "public.info"})
     * @ORM\Column(type="boolean", options={"default" : false})
     */
    private $isVerified = false;

    /**
     * @var string|null
     * @Groups({"public.checkout", "public.info"})
     * @ORM\Column(type="string", nullable=true)
     */
    private $mask;

    /**
     * @var string|null
     * @Groups({"public.checkout", "public.info"})
     * @ORM\Column(type="string", nullable=true)
     */
    private $type;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $token;

    /**
     * @var DateTimeInterface|null
     * @ORM\Column(type="date", nullable=true)
     */
    private $lifeTime;

    public function __construct(string $project, string $userRef, int $method)
    {
        $this->project = $project;
        $this->userRef = $userRef;
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return string
     */
    public function getUserRef(): string
    {
        return $this->userRef;
    }

    /**
     * @return string
     */
    public function getProject(): string
    {
        return $this->project;
    }

    /**
     * @return int
     */
    public function getMethod(): int
    {
        return $this->method;
    }

    /**
     * @return bool
     */
    public function getIsVerified(): bool
    {
        return $this->isVerified;
    }

    /**
     * @return string|null
     */
    public function getMask(): ?string
    {
        return $this->mask;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }


    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getLifeTime(): ?DateTimeInterface
    {
        return $this->lifeTime;
    }

    /**
     * @Groups({"created", "public.checkout", "public.info"})
     * @return string|null
     */
    public function getEnableTo(): ?string
    {
        return $this->lifeTime ? $this->lifeTime->format('m/y') : null;
    }

    /**
     * @ORM\PrePersist()
     * @throws Exception
     */
    public function generateHash(): void
    {
        $this->hash = Uuid::uuid4()->toString();
    }

    public function updateFromDto(CardDto $dto): void
    {
        if (!$this->isVerified) {
            $this->isVerified = $dto->getIsVerified() ?? $this->isVerified;
        }

        $this->mask = $dto->getMasked();
        $this->type = $dto->getType();
        $this->token = $dto->getToken();
        $this->lifeTime = $dto->getLifeTime();
    }
}
