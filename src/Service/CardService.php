<?php

namespace App\Service;

use App\Component\Payment\Model\Card as CardDto;
use App\DTO\ListResult;
use App\Entity\Card;
use App\Entity\PaymentMethodInterface;
use App\Exception\ObjectNotFoundException;
use App\Repository\CardRepository;
use App\Security\User;
use Doctrine\ORM\EntityManagerInterface;

class CardService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getAll(string $project, User $user): ListResult
    {
        /** @var CardRepository $repository */
        $repository = $this->em->getRepository(Card::class);

        $list = $repository->findBy(['project' => $project, 'userRef' => $user->getId()]);

        return new ListResult($list, count($list));
    }

    /**
     * @param string $project
     * @param User $user
     * @param int $method
     * @return Card
     */
    public function create(string $project, User $user, int $method = PaymentMethodInterface::FONDY_METHOD): Card
    {
        $card = new Card($project, $user->getId(), $method);

        $this->em->persist($card);
        $this->em->flush();

        return $card;
    }

    /**
     * @param string $project
     * @param User $user
     * @param string $hash
     * @throws ObjectNotFoundException
     */
    public function delete(string $project, User $user, string $hash): void
    {
        $card = $this->findCard($project, $user, $hash);
        $this->em->remove($card);
        $this->em->flush();
    }

    /**
     * @param string $cardHash
     * @param User $user
     * @param string $project
     * @param bool|null $isVerified
     * @return Card
     * @throws ObjectNotFoundException
     */
    public function findCard(string $project, User $user, string $cardHash, ?bool $isVerified = null): Card
    {
        /** @var CardRepository $repository */
        $repository = $this->em->getRepository(Card::class);

        $card = $repository->findByHash($project, $user->getId(), $cardHash);

        if (($card && $isVerified === null) || ($card && $card->getIsVerified() === $isVerified)) {
            return $card;
        }

        throw new ObjectNotFoundException(
            sprintf('Card by hash %s and project %s not found for user', $cardHash, $project)
        );
    }

    /**
     * @param Card $card
     * @param CardDto $dto
     * @return Card
     */
    public function update(Card $card, CardDto $dto): Card
    {
        $card->updateFromDto($dto);

        $this->em->persist($card);
        $this->em->flush();

        return $card;
    }

    public function updateFromPaymentResponse(string $project, string $userRef, int $method, CardDto $dto): Card
    {
        $card = $this->findByToken($project, $userRef, $method, $dto->getToken()) ??
            new Card($project, $userRef, $method);

        return $this->update($card, $dto);
    }

    private function findByToken(string $project, string $userRef, int $method, string $token): ?Card
    {
        /** @var CardRepository $repository */
        $repository = $this->em->getRepository(Card::class);

        return $repository->findOneBy(compact('project', 'userRef', 'method', 'token'));
    }
}
