<?php

namespace App\Entity;

use App\Entity\Traits\CreatedUpdatedTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderStatusHistoryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class OrderStatusHistory
{
    use CreatedUpdatedTrait;

    /**
     * @var integer
     * @Serializer\Groups({"admin.info"})
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Order
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", fetch="LAZY")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", nullable=false)
     */
    private $order;

    /**
     * @var integer
     * @Serializer\Groups({"admin.info"})
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @var array|null
     * @Serializer\Groups({"admin.info"})
     * @SWG\Property(type="object", example={"errorCode": "", "errorDescription": ""})
     * @ORM\Column(type="json", nullable=true)
     */
    private $data;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @Serializer\MaxDepth(1)
     * @return Order|null
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @param Order|null $order
     * @return OrderStatusHistory
     */
    public function setOrder(?Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return OrderStatusHistory
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData(?array $data)
    {
        $this->data = $data;

        return $this;
    }
}
