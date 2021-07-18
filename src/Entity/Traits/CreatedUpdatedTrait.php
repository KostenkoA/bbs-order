<?php

namespace App\Entity\Traits;

use Symfony\Component\Serializer\Annotation as Serializer;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

trait CreatedUpdatedTrait
{
    /**
     * @Serializer\Groups({"admin.list","admin.info","createdAt"})
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Serializer\Groups({"admin.info","updatedAt"})
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTimeInterface $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeInterface $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function createdAtNow(): void
    {
        $this->createdAt = new DateTime();
    }

    /**
     * @ORM\PreUpdate()
     * @ORM\PrePersist()
     */
    public function updatedAtNow(): void
    {
        $this->updatedAt = new DateTime();
    }
}
