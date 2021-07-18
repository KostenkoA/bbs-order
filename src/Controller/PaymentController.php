<?php

namespace App\Controller;

use App\Component\Payment\PaymentException;
use App\Controller\Common\ResponseHandlerTrait;
use App\Exception\ObjectNotFoundException;
use App\Security\User;
use App\Service\PaymentService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PaymentController extends AbstractController
{
    use ResponseHandlerTrait;

    /**
     * Create new Payment
     *
     * @SWG\Tag(name="Payment")
     * @SWG\Parameter(name="Project", in="header", type="string", description="Project name")
     * @SWG\Response(response="201",description="Payment created, redirect")
     *
     * @param $orderHash
     * @param Request $request
     * @param PaymentService $paymentService
     * @return RedirectResponse
     * @throws ObjectNotFoundException
     * @throws PaymentException
     */
    public function create($orderHash, Request $request, PaymentService $paymentService): RedirectResponse
    {
        return $this->redirect(
            $paymentService->newOrderPayment($orderHash, $request->headers->get('Project')),
            Response::HTTP_CREATED
        );
    }

    /**
     * @SWG\Tag(name="Card")
     * @SWG\Response(response="201", description="Payment created, redirect")
     *
     * @param $cardHash
     * @param TokenStorageInterface $tokenStorage
     * @param Request $request
     * @param PaymentService $paymentService
     * @return RedirectResponse
     * @throws ObjectNotFoundException
     * @throws PaymentException
     */
    public function cardVerifyCreate(
        $cardHash,
        TokenStorageInterface $tokenStorage,
        Request $request,
        PaymentService $paymentService
    ): RedirectResponse {
        /** @var User $user */
        $user = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser() : null;

        return $this->redirect(
            $paymentService->newCardVerificationPayment($request->headers->get('Project'), $user, $cardHash),
            Response::HTTP_CREATED
        );
    }

    /**
     * Checkout Payment
     * @SWG\Tag(name="Payment")
     * @SWG\Response(response="200", description="Payment checkout", @Model(type="App\Entity\Payment", groups={"public.checkout"}))
     *
     * @param $paymentHash
     * @param PaymentService $paymentService
     * @return JsonResponse
     * @throws ObjectNotFoundException
     * @throws PaymentException
     */
    public function checkout($paymentHash, PaymentService $paymentService): JsonResponse
    {
        return $this->handleResponse(
            $paymentService->checkoutOrderPayment($paymentHash),
            ['public.checkout'],
            Response::HTTP_OK
        );
    }
}
