<?php

namespace App\Controller;

use App\Component\Bonus\BonusException;
use App\Component\Delivery\DeliveryException;
use App\Component\Product\ProductSearchException;
use App\Component\RequestResponseException;
use App\Component\UserService\UserServiceException;
use App\Component\UserService\UserServiceResponseException;
use App\Controller\Common\ProjectHeaderTrait;
use App\Controller\Common\ResponseHandlerTrait;
use App\DTO\NewOrder;
use App\DTO\OrderSearch;
use App\Exception\ObjectNotFoundException;
use App\Form\Order\DeliveryAddressType;
use App\Form\Order\DeliveryBranchType;
use App\Form\Order\DeliveryShopType;
use App\Form\Order\OrderByAnonAutoRegisterType;
use App\Form\Order\OrderByOneClickType;
use App\Form\Order\OrderByRegisteredType;
use App\Form\Order\OrderType;
use App\Form\OrderSearchType;
use App\Security\User;
use App\Service\Order\CreateOrderService;
use App\Service\Order\OrderInfoService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Nelmio\ApiDocBundle\Annotation\Model;

class OrderController extends AbstractController
{
    use ResponseHandlerTrait;
    use ProjectHeaderTrait;

    /**
     * Create new order action by registered user
     *
     * @SWG\Tag(name="Order")
     *
     * @SWG\Parameter(name="Project", in="header", type="string", description="Project name")
     * @SWG\Parameter(in="body", name="Form", description="Combine with delivery forms",type="string", @Model(type=OrderByRegisteredType::class))
     * @SWG\Parameter(in="body", name="Delivery address form", type="string", @Model(type=DeliveryAddressType::class))
     * @SWG\Parameter(in="body", name="Delivery branch form", type="string", @Model(type=DeliveryBranchType::class))
     * @SWG\Parameter(in="body", name="Delivery shop form", type="string", @Model(type=DeliveryShopType::class))
     *
     * @SWG\Response(response="201", description="Created", @Model(type="App\Entity\Order", groups={"created"}))
     *
     * @SWG\Response(
     *     response="400",
     *     description="Order Form Error",
     *     @SWG\Schema(
     *      type="object",
     *      @SWG\Property(property="code", type="integer", description="Error code"),
     *      @SWG\Property(property="error", type="string", description="Error message"),
     *      @SWG\Property(property="errors", type="object", description="Form errors"),
     *     )
     * )
     *
     * @param TokenStorageInterface $tokenStorage
     * @param Request $request
     * @param CreateOrderService $createOrderService
     * @return JsonResponse
     * @throws BonusException
     * @throws DeliveryException
     * @throws UserServiceException
     * @throws UserServiceResponseException
     * @throws ProductSearchException
     * @throws RequestResponseException
     */
    public function create(
        TokenStorageInterface $tokenStorage,
        Request $request,
        CreateOrderService $createOrderService
    ): JsonResponse {
        $this->addProjectIfHeaderExist($request);

        $deliveryType = $request->request->get('deliveryType');

        $form = $this->createForm(OrderByRegisteredType::class, null, ['delivery_type' => $deliveryType]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var NewOrder $newOrder */
            $newOrder = $form->getData();

            /** @var User $user */
            $user = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser() : null;

            $order = $createOrderService->createByRegistered($newOrder, $user);

            return $this->handleResponse($order, ['created'], Response::HTTP_CREATED);
        }

        return $this->handleFormError($form);
    }

    /**
     * Create new order action by not registered user
     *
     * @SWG\Tag(name="Order")
     *
     * @SWG\Parameter(name="Project", in="header", type="string", description="Project name")
     * @SWG\Parameter(in="body", name="Form", description="Combine with delivery forms",type="string", @Model(type=OrderType::class))
     * @SWG\Parameter(in="body", name="Delivery address form", type="string", @Model(type=DeliveryAddressType::class))
     * @SWG\Parameter(in="body", name="Delivery branch form", type="string", @Model(type=DeliveryBranchType::class))
     * @SWG\Parameter(in="body", name="Delivery shop form", type="string", @Model(type=DeliveryShopType::class))
     *
     * @SWG\Response(response="201", description="Created", @Model(type="App\Entity\Order", groups={"created"}))
     *
     *
     * @param Request $request
     * @param CreateOrderService $createOrderService
     * @return JsonResponse
     * @throws ProductSearchException
     * @throws DeliveryException
     * @throws RequestResponseException
     */
    public function createByAnon(Request $request, CreateOrderService $createOrderService): JsonResponse
    {
        $this->addProjectIfHeaderExist($request);

        $deliveryType = $request->request->get('deliveryType');

        $form = $this->createForm(OrderType::class, null, ['delivery_type' => $deliveryType]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order = $createOrderService->createByAnon($form->getData());

            return $this->handleResponse($order, ['created'], Response::HTTP_CREATED);
        }

        return $this->handleFormError($form);
    }

    /**
     * Create new order action by not registered user, user auto register
     *
     * @SWG\Tag(name="Order")
     *
     * @SWG\Parameter(name="Project", in="header", type="string", description="Project name")
     * @SWG\Parameter(in="body", name="Form", description="Combine with delivery forms",type="string", @Model(type=OrderByAnonAutoRegisterType::class))
     * @SWG\Parameter(in="body", name="Delivery address form", type="string", @Model(type=DeliveryAddressType::class))
     * @SWG\Parameter(in="body", name="Delivery branch form", type="string", @Model(type=DeliveryBranchType::class))
     * @SWG\Parameter(in="body", name="Delivery shop form", type="string", @Model(type=DeliveryShopType::class))
     * @SWG\Response(response="201", description="Order created", @Model(type="App\Entity\Order", groups={"created"}))
     *
     * @param Request $request
     * @param CreateOrderService $createOrderService
     * @return JsonResponse
     * @throws ProductSearchException
     * @throws DeliveryException
     * @throws RequestResponseException
     */
    public function createByAnonRegister(Request $request, CreateOrderService $createOrderService): JsonResponse
    {
        $this->addProjectIfHeaderExist($request);

        $deliveryType = $request->request->get('deliveryType');

        $form = $this->createForm(OrderByAnonAutoRegisterType::class, null, ['delivery_type' => $deliveryType]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order = $createOrderService->createByAnonRegister($form->getData());

            return $this->handleResponse($order, ['created'], Response::HTTP_CREATED);
        }

        return $this->handleFormError($form);
    }

    /**
     * @SWG\Tag(name="Order")
     *
     * @SWG\Parameter(in="header", required=true, minLength=1, maxLength=25, type="string", name="Project", description="Project name")
     * @SWG\Parameter(in="body", name="Form", description="Order Form", @Model(type=OrderByOneClickType::class))
     * @SWG\Parameter(in="body", name="Form if deliveryType shop", @Model(type=DeliveryShopType::class))
     * @SWG\Response(response="201", description="Order created", @Model(type="App\Entity\Order", groups={"created"}))
     *
     * @param Request $request
     * @param CreateOrderService $createOrderService
     * @return JsonResponse
     * @throws DeliveryException
     * @throws ProductSearchException
     * @throws RequestResponseException
     */
    public function createByOneClick(Request $request, CreateOrderService $createOrderService): JsonResponse
    {
        $this->addProjectIfHeaderExist($request);

        $deliveryType = $request->request->get('deliveryType');

        $form = $this->createForm(OrderByOneClickType::class, null, ['delivery_type' => $deliveryType]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order = $createOrderService->createByOneClick($form->getData());

            return $this->handleResponse($order, ['created'], Response::HTTP_CREATED);
        }

        return $this->handleFormError($form);
    }

    /**
     * Order info item action
     * @SWG\Tag(name="Order")
     *
     * @SWG\Parameter(in="header", required=true, minLength=1, maxLength=25, type="string", name="Project", description="Project name")
     * @SWG\Response(response="200", description="Ok", @Model(type="App\Entity\Order", groups={"info", "createdAt"}))
     *
     * @param string $hash
     * @param TokenStorageInterface $tokenStorage
     * @param Request $request
     * @param OrderInfoService $orderService
     * @return JsonResponse
     * @throws ObjectNotFoundException
     */
    public function getByHash(
        string $hash,
        TokenStorageInterface $tokenStorage,
        Request $request,
        OrderInfoService $orderService
    ): JsonResponse {
        return $this->handleResponse(
            $orderService->findForUser(
                $hash,
                $this->getProjectName($request),
                $tokenStorage->getToken()->getUser()->getId()
            ),
            ['info', 'createdAt']
        );
    }

    /**
     * Order info item action
     * @SWG\Tag(name="Order")
     *
     * @SWG\Parameter(in="header", required=true, minLength=1, maxLength=25, type="string", name="Project", description="Project name")
     *
     * @SWG\Response(response="200", description="Ok", @Model(type="App\Entity\Order", groups={"info", "createdAt"}))
     *
     *
     * @param string $hash
     * @param Request $request
     * @param OrderInfoService $orderService
     * @return JsonResponse
     * @throws ObjectNotFoundException
     */
    public function getByHashAnon(string $hash, Request $request, OrderInfoService $orderService): JsonResponse
    {
        return $this->handleResponse(
            $orderService->findForUser($hash, $this->getProjectName($request), null),
            ['info', 'createdAt']
        );
    }

    /**
     * @SWG\Tag(name="Order")
     *
     * @SWG\Parameter(in="header", required=true, minLength=1, maxLength=25, type="string", name="Project", description="Project name")
     * @SWG\Parameter(in="body",name="Order search query",type="string",@Model(type=OrderSearchType::class))
     * @SWG\Response(
     *     response="200",
     *     description="Order list",
     *     @SWG\Schema(
     *     type="array",
     *     @SWG\Items(ref=@Model(type=App\DTO\OrderSearchResult::class, groups={"public.list", "createdAt"}))
     * ))
     * @param TokenStorageInterface $tokenStorage
     * @param OrderInfoService $service
     * @param Request $request
     * @return JsonResponse
     */
    public function getOrderList(
        TokenStorageInterface $tokenStorage,
        OrderInfoService $service,
        Request $request
    ): JsonResponse {
        $form = $this->createForm(OrderSearchType::class);
        $form->submit($request->query->all());

        if (!$form->isValid()) {
            return $this->handleFormError($form);
        }

        /** @var OrderSearch $orderSearch */
        $orderSearch = $form->getData();
        $orderSearch->projectName = $this->getProjectName($request);
        $orderSearch->setUser($tokenStorage->getToken()->getUser());

        return $this->handleResponse($service->getList($orderSearch), ['public.list', 'createdAt']);
    }
}
