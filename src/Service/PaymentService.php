<?php

namespace App\Service;

use App\Builder\PaymentBuilder;
use App\Component\Payment\CheckoutPaymentInterface;
use App\Component\Payment\PaymentException;
use App\Component\Payment\CardInterface;
use App\Entity\Card;
use App\Entity\Order;
use App\Entity\Payment;
use App\Exception\ObjectNotFoundException;
use App\Repository\PaymentRepository;
use App\Component\Payment\PaymentComponent;
use App\Security\User;
use App\Service\Order\OrderInfoService;
use App\Service\Subscription\SubscriptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use App\Component\Payment\Model\Card as CardResponse;

class PaymentService
{
    /**
     * @var PaymentComponent
     */
    private $paymentComponent;

    /**
     * @var OrderInfoService
     */
    private $orderInfoService;

    /**
     * @var SubscriptionService
     */
    private $subscriptionService;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var PaymentBuilder
     */
    private $builder;

    /**
     * @var NormalizerInterface
     */
    private $normalize;

    /**
     * @var string
     */
    private $defaultPaymentMethod;

    /**
     * @var CardService
     */
    private $cardService;


    public function __construct(
        PaymentComponent $paymentComponent,
        OrderInfoService $orderInfoService,
        SubscriptionService $subscriptionService,
        CardService $cardService,
        PaymentBuilder $builder,
        EntityManagerInterface $entityManager,
        NormalizerInterface $normalizer,
        string $defaultPaymentMethod
    ) {
        $this->paymentComponent = $paymentComponent;
        $this->orderInfoService = $orderInfoService;
        $this->subscriptionService = $subscriptionService;
        $this->cardService = $cardService;
        $this->em = $entityManager;
        $this->builder = $builder;
        $this->normalize = $normalizer;
        $this->defaultPaymentMethod = $defaultPaymentMethod;
    }

    /**
     * @param string $orderHash
     * @param string $projectName
     * @return string
     * @throws ObjectNotFoundException
     * @throws PaymentException
     */
    public function newOrderPayment(string $orderHash, string $projectName): string
    {
        $order = $this->orderInfoService->getByHash($orderHash, $projectName);

        $payment = $this->builder->buildForOrderNewPayment($order, $this->defaultPaymentMethod);

        $this->paymentComponent->checkPaymentEnable($payment);

        $this->em->persist($payment);
        $this->em->flush();

        $response = $this->paymentComponent->createPayment($payment);

        $payment->setStatusCreated();

        $payment->getOrder()->updatePaymentStatus();
        $this->em->merge($payment);
        $this->em->flush();

        return $response->getPaymentUrl();
    }

    /**
     * @param string $cardHash
     * @param User $user
     * @param string $project
     * @return string
     * @throws ObjectNotFoundException
     * @throws PaymentException
     */
    public function newCardVerificationPayment(string $project, User $user, string $cardHash): string
    {
        $card = $this->cardService->findCard($project, $user, $cardHash);

        if ($card && !$card->getIsVerified()) {
            $payment = $this->builder->buildForCardVerification($card);
            $this->em->persist($payment);
            $this->em->flush();

            $response = $this->paymentComponent->createPayment($payment);

            return $response->getPaymentUrl();
        }

        throw new ObjectNotFoundException('Card already verified');
    }

    /**
     * @param string $paymentHash
     * @return Payment
     * @throws ObjectNotFoundException
     * @throws PaymentException
     */
    public function checkoutOrderPayment(string $paymentHash): Payment
    {
        $payment = $this->getByHash($paymentHash);
        $response = $this->paymentComponent->checkoutPayment($payment);
        $order = $this->checkOrderPaymentResponse($payment, $response);

        if ($response instanceof CardInterface && $order) {
            $this->checkCardPaymentResponse($payment, $response->getCard(), $order);
        }

        return $payment;
    }

    /**
     * @param Order $order
     * @param Card $card
     * @return Order
     * @throws ObjectNotFoundException
     * @throws PaymentException
     */
    public function paymentOrderByCard(Order $order, Card $card): Order
    {
        if (
            !$card->getIsVerified() ||
            $order->getProjectName() !== $card->getProject() ||
            $order->getUserRef() !== $card->getUserRef()
        ) {
            throw new ObjectNotFoundException('Wrong card for order');
        }

        $payment = $this->builder->buildForOrderByToken($order, $card);

        $this->paymentComponent->checkPaymentEnable($payment);

        $this->em->persist($payment);
        $this->em->flush();

        $response = $this->paymentComponent->paymentByToken($payment);

        $this->checkOrderPaymentResponse($payment, $response);

        if ($response instanceof CardInterface && $order) {
            $this->checkCardPaymentResponse($payment, $response->getCard(), $order);
        }

        return $order;
    }


    /**
     * @param string $hash
     * @return Payment
     * @throws ObjectNotFoundException
     */
    public function getByHash(string $hash): Payment
    {
        /** @var PaymentRepository $repository */
        $repository = $this->em->getRepository(Payment::class);

        if ($payment = $repository->findOneByHash($hash)) {
            return $payment;
        }

        throw new ObjectNotFoundException(sprintf('Payment by hash %s not found', $hash));
    }

    private function checkCardPaymentResponse(Payment $payment, CardResponse $cardModel, ?Order $order): void
    {
        if ($payment->isTypeCardVerification() && $payment->getCard()) {
            $this->cardService->update($payment->getCard(), $cardModel);
        } elseif ($order && $order->getSubscription()) {
            $card = $this->cardService->updateFromPaymentResponse(
                $order->getProjectName(),
                $order->getUserRef(),
                $payment->getMethod(),
                $cardModel
            );

            $this->subscriptionService->updateByCard($order->getSubscription(), $card);
        }
    }

    private function checkOrderPaymentResponse(Payment $payment, CheckoutPaymentInterface $response): ?Order
    {
        $payment->setStatus($response->getPaymentStatus());
        $payment->setResponse($this->normalize->normalize($response, 'array', ['groups' => ['save']]));

        $this->em->persist($payment);
        $this->em->flush();

        $order = $payment->getOrder();
        if ($order) {
            $order->updatePaymentStatus();
            $this->em->persist($order);
            $this->em->flush();
        }

        return $order;
    }
}
