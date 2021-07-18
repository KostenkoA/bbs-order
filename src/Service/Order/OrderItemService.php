<?php

namespace App\Service\Order;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

class OrderItemService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * OrderExistsService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $projectName
     * @param string $userRef
     * @param string $productSlug
     * @return bool
     */
    public function checkProductItemExist(string $projectName, string $userRef, string $productSlug): bool
    {
        $orderRepository = $this->entityManager->getRepository(Order::class);

        return (bool)$orderRepository->getCountByItemSlug($projectName, $productSlug, $userRef);
    }
}
