<?php

namespace App\Component\Payment\Model;

use DateTime;

class Card
{
    /**
     * @var boolean|null
     */
    private $isVerified;

    /**
     * @var string
     */
    private $masked;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string|null
     */
    private $type;

    /**
     * @var DateTime
     */
    private $lifeTime;

    /**
     * Card constructor.
     * @param bool|null $isVerified
     * @param string $masked
     * @param string $token
     * @param string|null $type
     * @param DateTime $lifeTime
     */
    public function __construct(?bool $isVerified, string $masked, string $token, ?string $type, DateTime $lifeTime)
    {
        $this->isVerified = $isVerified;
        $this->masked = $masked;
        $this->token = $token;
        $this->type = $type;
        $this->lifeTime = $lifeTime;
    }

    /**
     * @return bool|null
     */
    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    /**
     * @return string
     */
    public function getMasked(): string
    {
        return $this->masked;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return DateTime
     */
    public function getLifeTime(): DateTime
    {
        return $this->lifeTime;
    }
}
