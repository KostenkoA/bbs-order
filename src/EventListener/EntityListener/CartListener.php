<?php

namespace App\EventListener\EntityListener;

use App\Entity\Card;
use App\Entity\Subscription;
use Doctrine\ORM\Event\LifecycleEventArgs;

class CartListener
{
    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof Card) {
            $entityManager = $args->getObjectManager();

            $subscriptionRepository = $entityManager->getRepository(Subscription::class);
            $subscriptionRepository->updateStatusByCard($entity, Subscription::STATUS_NOT_APPROVED);
        }
    }
}
