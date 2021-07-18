<?php

namespace App\Controller\Admin;

use App\Component\Delivery\DeliveryException;
use App\Component\Product\ProductSearchException;
use App\Component\Product\ProductSearchResponseException;
use App\Controller\Common\ResponseHandlerTrait;
use App\Exception\ObjectNotFoundException;
use App\Form\Order\AdminOrderSearchType;
use App\Form\Order\AdminOrderByAnonAutoregisterType;
use App\Form\Order\AdminOrderByRegisteredType;
use App\Form\Order\DeliveryAddressType;
use App\Form\Order\DeliveryBranchType;
use App\Form\Order\OrderType;
use App\Security\User;
use App\Service\Order\CreateOrderService;
use App\Service\Order\Order1CService;
use App\Service\Order\OrderInfoService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;

class AdminOrderController extends AbstractController
{
    use ResponseHandlerTrait;

    /**
     * @SWG\Tag(name="Admin Order")
     * @SWG\Parameter(
     *     in="body",
     *     name="Order search query",
     *     type="string",
     *     @Model(type=AdminOrderSearchType::class)
     * )
     * @SWG\Response(
     *     response="200",
     *     description="Order list",
     *     @SWG\Schema(
     *     type="array",
     *     @SWG\Items(ref=@Model(type=\App\DTO\OrderSearchResult::class, groups={"admin.list"}))
     * ))
     * @param OrderInfoService $service
     * @param Request $request
     * @return JsonResponse
     */
    public function getOrderList(OrderInfoService $service, Request $request): JsonResponse
    {
        $form = $this->createForm(AdminOrderSearchType::class);
        $form->submit($request->query->all());

        if (!$form->isValid()) {
            return $this->handleFormError($form);
        }

        return $this->handleResponse($service->getAdminList($form->getData()), ['admin.list']);
    }

    /**
     * @SWG\Tag(name="Admin Order")
     * @SWG\Response(
     *     response="200",
     *     description="Ok",
     *     @Model(type="App\Entity\Order", groups={"admin.info"})
     * )
     * @param int $id
     * @param OrderInfoService $service
     * @return JsonResponse
     * @throws ObjectNotFoundException
     */
    public function getById(int $id, OrderInfoService $service): JsonResponse
    {
        return $this->handleResponse($service->findById($id), ['admin.info']);
    }

    /**
     * @SWG\Tag(name="Admin Order")
     * @SWG\Response(response="200", description="Ok")
     *
     * @param int $id
     * @param OrderInfoService $orderInfoService
     * @param Order1CService $order1CService
     * @return JsonResponse
     * @throws ObjectNotFoundException
     */
    public function sendTo1c(int $id, OrderInfoService $orderInfoService, Order1CService $order1CService): JsonResponse
    {
        $order = $orderInfoService->findById($id);

        if ($order) {
            $order1CService->sendTo1c($order);
        }

        return $this->handleResponse();
    }


    /**
     * @SWG\Tag(name="Admin Order")
     *
     * @SWG\Parameter(
     *     in="header",
     *     required=true,
     *     name="Project",
     *     type="string",
     *     minLength=1,
     *     maxLength=25,
     *     description="Project name which the request comes from"
     * )
     *
     * @SWG\Parameter(
     *     in="body",
     *     name="Order form params",
     *     description="Combine order form with delivery address form or delivery branch form",
     *     type="string",
     *     @Model(type=OrderType::class)
     * )
     *
     * @SWG\Parameter(
     *     in="body",
     *     name="Delivery address form params",
     *     type="string",
     *     @Model(type=DeliveryAddressType::class)
     * )
     *
     * @SWG\Parameter(
     *     in="body",
     *     name="Delivery branch form params",
     *     type="string",
     *     @Model(type=DeliveryBranchType::class)
     * )
     *
     * @SWG\Response(
     *     response="201",
     *     description="Order created",
     *     @Model(type="App\Entity\Order", groups={"created"})
     * )
     *
     * @param Request $request
     * @param CreateOrderService $createOrderService
     * @return JsonResponse
     * @throws ProductSearchException
     * @throws DeliveryException
     * @throws ProductSearchResponseException
     */
    public function createByAnonRegister(Request $request, CreateOrderService $createOrderService): JsonResponse
    {
        $deliveryType = $request->request->get('deliveryType');

        $form = $this->createForm(
            AdminOrderByAnonAutoregisterType::class,
            null,
            ['delivery_type' => $deliveryType]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order = $createOrderService->createByAnonRegister($form->getData(), $request->headers->get('Project'));

            return $this->handleResponse($order, ['created', 'admin.info'], Response::HTTP_CREATED);
        }

        return $this->handleFormError($form);
    }

    /**
     * @SWG\Tag(name="Admin Order")
     *
     * @SWG\Parameter(
     *     in="header",
     *     required=true,
     *     name="Project",
     *     type="string",
     *     minLength=1,
     *     maxLength=25,
     *     description="Project name which the request comes from"
     * )
     *
     * @SWG\Parameter(
     *     in="body",
     *     name="Order form params",
     *     description="Combine order form with delivery address form or delivery branch form",
     *     type="string",
     *     @Model(type=AdminOrderByRegisteredType::class)
     * )
     *
     * @SWG\Parameter(
     *     in="body",
     *     name="Delivery address form params",
     *     type="string",
     *     @Model(type=DeliveryAddressType::class)
     * )
     *
     * @SWG\Parameter(
     *     in="body",
     *     name="Delivery branch form params",
     *     type="string",
     *     @Model(type=DeliveryBranchType::class)
     * )
     *
     * @SWG\Response(
     *     response="201",
     *     description="Order created",
     *     @Model(type="App\Entity\Order", groups={"created"})
     * )
     *
     * @param Request $request
     * @param CreateOrderService $createOrderService
     * @return JsonResponse
     * @throws ProductSearchException
     * @throws DeliveryException
     * @throws ProductSearchResponseException
     */
    public function createByUser(Request $request, CreateOrderService $createOrderService): JsonResponse
    {
        $deliveryType = $request->request->get('deliveryType');

        $form = $this->createForm(AdminOrderByRegisteredType::class, null, ['delivery_type' => $deliveryType]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $orderDto = $form->getData();
            $user = new User($orderDto->userRef);
            $order = $createOrderService->createByRegistered(
                $form->getData(),
                $user,
                $request->headers->get('Project')
            );

            return $this->handleResponse($order, ['created', 'admin.info'], Response::HTTP_CREATED);
        }

        return $this->handleFormError($form);
    }
}
