<?php

namespace App\Entity;

use App\DTO\BasketChecked\BasketCheckedGiftList;
use App\DTO\BasketChecked\BasketCheckedGiftNomenclature;
use App\DTO\BasketChecked\BasketCheckedItem;
use App\DTO\BasketChecked\BasketCheckedModel;
use App\Entity\Traits\CreatedUpdatedTrait;
use App\Component\Product\Response\Product;
use App\Entity\Value\OrderItemDiscount;
use App\Entity\Value\OrderItemGiftDiscount;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderItemRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class OrderItem implements ItemReserveStateInterface
{
    use CreatedUpdatedTrait;

    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     * @Serializer\Groups({"info","admin.info"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $reserveState;

    /**
     * @var DateTimeInterface|null
     * @Serializer\Groups({"info","admin.info"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deliveryDate;

    /**
     * @var string
     * @Serializer\Groups({"info","admin.info"})
     * @ORM\Column(type="string")
     */
    private $productId;

    /**
     * @var string
     * @Serializer\Groups({"info","admin.info"})
     * @ORM\Column(type="string")
     */
    private $internalId;

    /**
     * @var string
     * @Serializer\Groups({"public.list","info","admin.info","email"})
     * @ORM\Column(type="string")
     */
    private $displayArticle;

    /**
     * @var string
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="string")
     */
    private $slug;

    /**
     * @var string
     * @Serializer\Groups({"public.list","info","admin.info","email"})
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var string
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="string", length=255)
     */
    private $titleUkr;

    /**
     * @var array
     * @SWG\Property(type="object", example={"slug": "bryuki", "title": "Брюки", "ordering": 32, "titleUkr": "Брюки"})
     * @Serializer\Groups({"info","admin.info"})
     * @ORM\Column(type="json")
     */
    private $category = [];

    /**
     * @var array
     * @SWG\Property(type="object", example={"slug": "odezhda", "title": "Одеж", "ordering": 32, "titleUkr": "Одеж"})
     * @Serializer\Groups({"info","admin.info"})
     * @ORM\Column(type="json")
     */
    private $folderCategory = [];

    /**
     * @var array
     * @SWG\Property(type="object", example={"slug": "molo", "title": "Molo", "ordering": 32, "titleUkr": "Molo"})
     * @Serializer\Groups({"info","admin.info"})
     * @ORM\Column(type="json")
     */
    private $brand = [];

    /**
     * @var array|null
     * @SWG\Property(type="object", example={"id":"4783","slug": "belyi", "title": "Белый", "ordering": 17, "titleUkr": "Білий"})
     * @ORM\Column(type="json", nullable=true)
     * @Serializer\Groups({"info","admin.info"})
     */
    private $colorPresentation;

    /**
     * @var array|null
     * @SWG\Property(type="object", example={"id":"50", "slug": "50", "title": "50", "ordering": 32, "titleUkr": "50"})
     * @ORM\Column(type="json", nullable=true)
     * @Serializer\Groups({"info","admin.info"})
     */
    private $sizePresentation;

    /**
     * @var array|null
     * @SWG\Property(
     *     type="object",
     *     example={"slug": "12-24-mes", "title": "12 - 24 мес", "gender": 1, "ordering": 6, "titleUkr": "12 - 24 мес"}
     *     )
     * @Serializer\Groups({"info","admin.info"})
     * @ORM\Column(type="json", nullable=true)
     */
    private $ageCategory;

    /**
     * @var array
     * @SWG\Property(type="array",
     *     @SWG\Items(type="object",
     *     example={"link": "17fb33a530821ab80a8b900e8520c342.jpeg","metaImg": false,"hash": "","title": ""}
     *     )
     * )
     * @Serializer\Groups({"public.list","info","admin.info","email"})
     * @ORM\Column(type="json", nullable=true)
     */
    private $images;

    /**
     * @var float|null
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="decimal", nullable=true)
     */
    private $recommendedPrice;

    /**
     * @var float
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="decimal", scale=0)
     */
    private $price;

    /**
     * @var int
     * @Serializer\Groups({"public.list","info","admin.info","email"})
     * @ORM\Column(type="smallint")
     */
    private $quantity;

    /**
     * @var int|null
     * @Serializer\Groups({"public.list","info","admin.info","email"})
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $expectedQuantity;

    /**
     * @var float
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="decimal", scale=0)
     */
    private $totalPrice = 0.0;

    /**
     * @var float
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="decimal", scale=0)
     */
    private $discountAmount = 0.0;

    /**
     * @var float
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="decimal", scale=0)
     */
    private $cost = 0.0;

    /**
     * @var OrderItemDiscount[]|null
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="json_document", options={}, nullable=true)
     */
    private $discounts;

    /**
     * @var OrderItemGiftDiscount|null
     * @Serializer\Groups({"info","admin.info","email"})
     * @ORM\Column(type="json_document", options={}, nullable=true)
     */
    private $giftDiscount;

    /**
     * @var Order
     * @Serializer\MaxDepth(1)
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="orderItems", fetch="LAZY")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    public function __construct(string $internalId, ?int $expectedQuantity, Order $order)
    {
        $this->internalId = $internalId;
        $this->expectedQuantity = $expectedQuantity;
        $this->order = $order;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getReserveState(): ?int
    {
        return $this->reserveState;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDeliveryDate(): ?DateTimeInterface
    {
        return $this->deliveryDate;
    }

    /**
     * @return string|null
     */
    public function getProductId(): ?string
    {
        return $this->productId;
    }

    /**
     * @return string|null
     */
    public function getInternalId(): ?string
    {
        return $this->internalId;
    }

    /**
     * @return string|null
     */
    public function getDisplayArticle(): ?string
    {
        return $this->displayArticle;
    }

    /**
     * @return mixed
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getTitleUkr(): ?string
    {
        return $this->titleUkr;
    }

    /**
     * @return array
     */
    public function getCategory(): ?array
    {
        return $this->category;
    }

    /**
     * @return array
     */
    public function getFolderCategory(): ?array
    {
        return $this->folderCategory;
    }

    /**
     * @return array|null
     */
    public function getBrand(): ?array
    {
        return $this->brand;
    }

    /**
     * @return array|null
     */
    public function getColorPresentation(): ?array
    {
        return !empty($this->colorPresentation) ? $this->colorPresentation : null;
    }

    /**
     * @return array|null
     */
    public function getSizePresentation(): ?array
    {
        return !empty($this->sizePresentation) ? $this->sizePresentation : null;
    }

    /**
     * @return array|null
     */
    public function getAgeCategory(): ?array
    {
        return !empty($this->ageCategory) ? $this->ageCategory : null;
    }

    /**
     * @return array|null
     */
    public function getImages(): ?array
    {
        return $this->images;
    }

    /**
     * @return float|null
     */
    public function getRecommendedPrice(): ?float
    {
        return $this->recommendedPrice;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @return int|null
     */
    public function getExpectedQuantity(): ?int
    {
        return $this->expectedQuantity;
    }


    /**
     * @return mixed
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * @return float
     */
    public function getDiscountAmount(): float
    {
        return $this->discountAmount;
    }

    /**
     * @return float
     */
    public function getCost(): float
    {
        return $this->cost;
    }

    /**
     * @return OrderItemDiscount[]|null
     */
    public function getDiscounts(): ?array
    {
        return $this->discounts;
    }

    /**
     * @return OrderItemGiftDiscount|null
     */
    public function getGiftDiscount(): ?OrderItemGiftDiscount
    {
        return $this->giftDiscount;
    }

    /**
     * @return bool
     * @Serializer\Groups({"info","admin.info","email"})
     */
    public function getIsGift(): bool
    {
        return (bool)($this->giftDiscount ?? false);
    }

    /**
     * @return Order|null
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @param Order|null $order
     * @return OrderItem
     */
    public function setOrder(?Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @return OrderItem
     */
    public function calculateTotalPrice(): self
    {
        $this->totalPrice = (float)$this->getPrice() * (int)$this->getQuantity();
        $this->cost = $this->totalPrice;

        return $this;
    }

    /**
     * @param int $reserveState
     * @param DateTimeInterface|null $deliveryDate
     */
    public function updateFrom1c(int $reserveState, ?DateTimeInterface $deliveryDate): void
    {
        $this->reserveState = $reserveState;
        $this->deliveryDate = $deliveryDate;
    }

    public function fillFromBasketCheckedItem(BasketCheckedItem $checkedItem): void
    {
        $this->quantity = $checkedItem->getQuantity();

        if ($checkedItem->getProduct()) {
            $this->fillFromProduct($checkedItem->getProduct());
        }

        if ($basketCheckedItem = $checkedItem->getChecked()) {
            $this->fillFromBasketCheckedModel($basketCheckedItem);
        }
    }

    public function fillByGift(BasketCheckedGiftNomenclature $item, BasketCheckedGiftList $giftList): void{
        $this->quantity = $item->getQuantity();
        $this->fillFromProduct($item->getProduct());
        $this->fillGiftDiscount($giftList);
    }

    public function fillFromProduct(Product $product): void
    {
        $this->productId = (string)$product->productId;
        $this->internalId = (string)$product->intervalId;
        $this->displayArticle = (string)$product->displayArticle;
        $this->slug = (string)$product->slug;
        $this->title = (string)$product->title;
        $this->titleUkr = (string)$product->titleUkr;
        $this->category = (array)$product->category;
        $this->folderCategory = (array)$product->folderCategory;
        $this->brand = (array)$product->brand;
        $this->colorPresentation = $product->colorPresentation ? (array)$product->colorPresentation : null;
        $this->sizePresentation = $product->sizePresentation ? (array)$product->sizePresentation : null;
        $this->ageCategory = $product->ageCategory ? (array)$product->ageCategory : null;
        $this->images = (array)$product->images;
        $this->price = (float)$product->sellingPrice;
        $this->recommendedPrice = $product->recommendedPrice && (float)$product->recommendedPrice !== (float)$product->sellingPrice ?
            (float)$product->recommendedPrice : null;

        $this->calculateTotalPrice();
    }

    protected function fillFromBasketCheckedModel(BasketCheckedModel $checkedModel): void
    {
        $this->discounts = null;
        foreach ($checkedModel->getDiscounts() ?? [] as $discount) {
            $this->discounts[] = new OrderItemDiscount(
                $discount->getId(),
                $discount->getTitle(),
                $discount->getAmount()
            );
        }
        $this->price = $checkedModel->getPrice();
        $this->discountAmount = $checkedModel->getDiscountAmount();

        $this->calculateTotalPrice();
        $this->cost = $checkedModel->getCost();
    }

    public function fillGiftDiscount(BasketCheckedGiftList $giftList): void
    {
        $this->giftDiscount = new OrderItemGiftDiscount(
            $giftList->getId(),
            $giftList->getName(),
            $giftList->getToNomenclature()
        );

        $this->price = 0;
        $this->calculateTotalPrice();
    }
}
