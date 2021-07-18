<?php

namespace App\Controller\Admin;

use App\Component\Bonus\BonusException;
use App\Component\Delivery\DeliveryException;
use App\Component\Product\ProductSearchException;
use App\Component\RequestResponseException;
use App\Component\UserService\UserServiceException;
use App\Component\UserService\UserServiceResponseException;
use App\Controller\Common\ResponseHandlerTrait;
use App\DTO\Subscription\AdminSubscriptionSearch;
use App\DTO\Subscription\Subscription;
use App\Exception\ObjectNotFoundException;
use App\Exception\SubscriptionException;
use App\Form\Subscription\AdminSubscriptionOrderCreateType;
use App\Form\Subscription\AdminSubscriptionPlanningType;
use App\Form\Subscription\AdminSubscriptionSearchType;
use App\Form\Subscription\AdminSubscriptionType;
use App\Security\User;
use App\Service\Subscription\AdminSubscriptionService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use App\DTO\BasketChecked\BasketCheckedItem;

class AdminSubscriptionController extends AbstractController
{
    use ResponseHandlerTrait;

    /**
     * @SWG\Tag(name="Admin subscription")
     * @SWG\Parameter(name="form", in="body", description="Form", @Model(type=AdminSubscriptionSearchType::class)))
     * @SWG\Response(
     *     response=200,
     *     description="Subscription",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="items", type="array", @SWG\Items(ref=@Model(type=\App\Entity\Subscription::class, groups={"list","admin.list"}))),
     *         @SWG\Property(property="total", type="integer"),
     *     )
     * )
     *
     * @param Request $request
     * @param AdminSubscriptionService $service
     * @return JsonResponse
     */
    public function getList(Request $request, AdminSubscriptionService $service): JsonResponse
    {
        $form = $this->createForm(AdminSubscriptionSearchType::class, null, ['method' => Request::METHOD_GET]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var AdminSubscriptionSearch $dto */
            $dto = $form->getData();

            return $this->handleResponse(
                $service->getList($dto),
                ['list', 'admin.list']
            );
        }

        return $this->handleFormError($form);
    }

    /**
     * @SWG\Tag(name="Admin subscription")
     * @SWG\Parameter(name="form", in="body", description="Form", @Model(type=AdminSubscriptionPlanningType::class)))
     * @SWG\Response(
     *     response=200,
     *     description="Subscription",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=BasketCheckedItem::class, groups={"subscription-planning"}))
     *     )
     * )
     *
     * @param Request $request
     * @param AdminSubscriptionService $service
     * @return JsonResponse
     * @throws Exception
     */
    public function productPlanningList(Request $request, AdminSubscriptionService $service): JsonResponse
    {
        $form = $this->createForm(AdminSubscriptionPlanningType::class, null, ['method' => Request::METHOD_GET]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var AdminSubscriptionSearch $dto */
            $dto = $form->getData();

            return $this->handleResponse($service->getProductPlanningList($dto), ['subscription-planning']);
        }

        return $this->handleFormError($form);
    }

    /**
     * @SWG\Tag(name="Admin subscription")
     * @SWG\Response(response=200, description="ok", @Model(type=\App\Entity\Subscription::class, groups={"admin.info"}))
     *
     * @param int $id
     * @param AdminSubscriptionService $service
     * @return JsonResponse
     * @throws ObjectNotFoundException
     */
    public function find(int $id, AdminSubscriptionService $service): JsonResponse
    {
        return $this->handleResponse($service->find($id), ['admin.info']);
    }

    /**
     * @SWG\Tag(name="Admin subscription")
     * @SWG\Parameter(name="form", in="body", description="Form", @Model(type=AdminSubscriptionType::class)))
     * @SWG\Response(response=200, description="ok", @Model(type=\App\Entity\Subscription::class, groups={"admin.info"}))
     *
     *
     * @param int $id
     * @param Request $request
     * @param AdminSubscriptionService $service
     * @return JsonResponse
     * @throws ObjectNotFoundException
     */
    public function update(int $id, Request $request, AdminSubscriptionService $service): JsonResponse
    {
        $deliveryType = $request->request->get('deliveryType');

        $form = $this->createForm(
            AdminSubscriptionType::class,
            null,
            ['method' => Request::METHOD_PUT, 'delivery_type' => $deliveryType]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Subscription $dto */
            $dto = $form->getData();

            return $this->handleResponse(
                $service->update($id, $dto),
                ['admin.info']
            );
        }

        return $this->handleFormError($form);
    }

    /**
     * @SWG\Tag(name="Admin subscription")
     * @SWG\Response(response=200, description="ok")
     *
     * @param int $id
     * @param AdminSubscriptionService $service
     * @return JsonResponse
     * @throws ObjectNotFoundException
     */
    public function delete(int $id, AdminSubscriptionService $service): JsonResponse
    {
        $service->delete($id);

        return $this->handleResponse();
    }

    /**
     * @SWG\Tag(name="Admin subscription")
     * @SWG\Response(
     *     response=200,
     *     description="Order",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="items", type="array", @SWG\Items(ref=@Model(type=\App\Entity\Order::class, groups={"list","admin.list","createdAt"}))),
     *         @SWG\Property(property="total", type="integer"),
     *     )
     * )
     *
     * @param int $id
     * @param AdminSubscriptionService $service
     * @return JsonResponse
     * @throws ObjectNotFoundException
     */
    public function getOrderList(int $id, AdminSubscriptionService $service): JsonResponse
    {
        return $this->handleResponse($service->getOrderList($id), ['list', 'admin.list', 'createdAt']);
    }

    /**
     * @SWG\Tag(name="Admin subscription")
     * @SWG\Response(response="201", description="ok", @Model(type="App\Entity\Order", groups={"created", "admin.info"}))
     *
     * @param int $id
     * @param Request $request
     * @param AdminSubscriptionService $service
     * @return JsonResponse
     * @throws BonusException
     * @throws DeliveryException
     * @throws ObjectNotFoundException
     * @throws ProductSearchException
     * @throws RequestResponseException
     * @throws UserServiceException
     * @throws UserServiceResponseException
     * @throws SubscriptionException
     */
    public function createOrder(int $id, Request $request, AdminSubscriptionService $service): JsonResponse
    {
        $form = $this->createForm(AdminSubscriptionOrderCreateType::class, null, ['method' => Request::METHOD_POST]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order = $service->createOrder($id, $form->getData());

            return $this->handleResponse($order, ['created', 'admin.info'], Response::HTTP_CREATED);
        }

        return $this->handleFormError($form);
    }
}
