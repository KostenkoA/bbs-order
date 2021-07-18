<?php

namespace App\Controller;

use App\Controller\Common\ProjectHeaderTrait;
use App\Controller\Common\ResponseHandlerTrait;
use App\DTO\Project;
use App\DTO\Subscription\Subscription;
use App\DTO\Subscription\SubscriptionCard;
use App\Exception\ObjectNotFoundException;
use App\Exception\SubscriptionException;
use App\Form\ProjectType;
use App\Form\Subscription\SubscriptionCardType;
use App\Form\Subscription\SubscriptionType;
use App\Security\User;
use App\Service\Subscription\SubscriptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;

class SubscriptionController extends AbstractController
{
    use ResponseHandlerTrait;
    use ProjectHeaderTrait;

    /**
     * @SWG\Tag(name="Subscription")
     * @SWG\Parameter(name="Project", in="header", type="string", description="Project name")
     * @SWG\Parameter(name="form", in="body", description="Form", @Model(type=ProjectType::class)))
     * @SWG\Response(
     *     response=200,
     *     description="Subscription",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="items", type="array", @SWG\Items(ref=@Model(type=\App\Entity\Subscription::class, groups={"list","public.list"}))),
     *         @SWG\Property(property="total", type="integer"),
     *     )
     * )
     *
     * @param TokenStorageInterface $tokenStorage
     * @param Request $request
     * @param SubscriptionService $service
     * @return JsonResponse
     */
    public function getList(
        TokenStorageInterface $tokenStorage,
        Request $request,
        SubscriptionService $service
    ): JsonResponse {
        $this->addProjectIfHeaderExist($request);
        $form = $this->createForm(ProjectType::class, null, ['method' => Request::METHOD_GET]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Project $dto */
            $dto = $form->getData();
            /** @var User $user */
            $user = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser() : null;

            return $this->handleResponse($service->getAll($dto->project, $user), ['list', 'public.list']);
        }

        return $this->handleFormError($form);
    }

    /**
     * @SWG\Tag(name="Subscription")
     * @SWG\Parameter(name="Project", in="header", type="string", description="Project name")
     * @SWG\Parameter(name="form", in="body", description="Form", @Model(type=ProjectType::class)))
     * @SWG\Response(response=200, description="ok", @Model(type=\App\Entity\Subscription::class, groups={"public.info"}))
     *
     * @param int $id
     * @param TokenStorageInterface $tokenStorage
     * @param Request $request
     * @param SubscriptionService $service
     * @return JsonResponse
     * @throws ObjectNotFoundException
     */
    public function find(
        int $id,
        TokenStorageInterface $tokenStorage,
        Request $request,
        SubscriptionService $service
    ): JsonResponse {
        $this->addProjectIfHeaderExist($request);
        $form = $this->createForm(ProjectType::class, null, ['method' => Request::METHOD_GET]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Project $dto */
            $dto = $form->getData();
            /** @var User $user */
            $user = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser() : null;

            return $this->handleResponse($service->find($dto->project, $user, $id), ['public.info']);
        }

        return $this->handleFormError($form);
    }

    /**
     * @SWG\Tag(name="Subscription")
     * @SWG\Parameter(name="Project", in="header", type="string", description="Project name")
     * @SWG\Parameter(name="form", in="body", description="Form", @Model(type=ProjectType::class)))
     * @SWG\Response(response=200, description="subscription object or empty", @Model(type=\App\Entity\Subscription::class, groups={"public.info"}))
     *
     *
     * @param TokenStorageInterface $tokenStorage
     * @param Request $request
     * @param SubscriptionService $service
     * @return JsonResponse
     */
    public function findDefault(
        TokenStorageInterface $tokenStorage,
        Request $request,
        SubscriptionService $service
    ): JsonResponse {
        $this->addProjectIfHeaderExist($request);
        $form = $this->createForm(ProjectType::class, null, ['method' => Request::METHOD_GET]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Project $dto */
            $dto = $form->getData();
            /** @var User $user */
            $user = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser() : null;

            return $this->handleResponse($service->findDefault($dto->project, $user), ['public.info']);
        }

        return $this->handleFormError($form);
    }

    /**
     * @SWG\Tag(name="Subscription")
     * @SWG\Parameter(name="Project", in="header", type="string", description="Project name")
     * @SWG\Parameter(name="form", in="body", description="Form", @Model(type=SubscriptionType::class)))
     * @SWG\Response(response=200, description="ok", @Model(type=\App\Entity\Subscription::class, groups={"public.info"}))
     *
     *
     * @param int $id
     * @param TokenStorageInterface $tokenStorage
     * @param Request $request
     * @param SubscriptionService $service
     * @return JsonResponse
     * @throws ObjectNotFoundException
     */
    public function update(
        int $id,
        TokenStorageInterface $tokenStorage,
        Request $request,
        SubscriptionService $service
    ): JsonResponse {
        $this->addProjectIfHeaderExist($request);
        $deliveryType = $request->request->get('deliveryType');

        $form = $this->createForm(
            SubscriptionType::class,
            null,
            ['method' => Request::METHOD_PUT, 'delivery_type' => $deliveryType]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Subscription $dto */
            $dto = $form->getData();
            /** @var User $user */
            $user = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser() : null;

            return $this->handleResponse($service->update($dto->project, $user, $id, $dto), ['public.info']);
        }

        return $this->handleFormError($form);
    }

    /**
     * update subscription card, card must be verified
     *
     * @SWG\Tag(name="Subscription")
     * @SWG\Parameter(name="Project", in="header", type="string", description="Project name")
     * @SWG\Parameter(name="form", in="body", description="Form", @Model(type=SubscriptionCardType::class)))
     * @SWG\Response(response=200, description="ok", @Model(type=\App\Entity\Subscription::class, groups={"public.info"}))
     *
     *
     * @param int $id
     * @param TokenStorageInterface $tokenStorage
     * @param Request $request
     * @param SubscriptionService $service
     * @return JsonResponse
     * @throws ObjectNotFoundException
     * @throws SubscriptionException
     */
    public function updateCard(
        int $id,
        TokenStorageInterface $tokenStorage,
        Request $request,
        SubscriptionService $service
    ): JsonResponse {
        $this->addProjectIfHeaderExist($request);

        $form = $this->createForm(
            SubscriptionCardType::class,
            null,
            ['method' => Request::METHOD_PUT]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var SubscriptionCard $dto */
            $dto = $form->getData();
            /** @var User $user */
            $user = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser() : null;

            return $this->handleResponse($service->updateCard($dto->project, $user, $id, $dto), ['public.info']);
        }

        return $this->handleFormError($form);
    }

    /**
     * @SWG\Tag(name="Subscription")
     * @SWG\Parameter(name="Project", in="header", type="string", description="Project name")
     * @SWG\Parameter(name="form", in="body", description="Form", @Model(type=ProjectType::class)))
     * @SWG\Response(response=200, description="ok")
     *
     * @param int $id
     * @param TokenStorageInterface $tokenStorage
     * @param Request $request
     * @param SubscriptionService $service
     * @return JsonResponse
     * @throws ObjectNotFoundException
     */
    public function delete(
        int $id,
        TokenStorageInterface $tokenStorage,
        Request $request,
        SubscriptionService $service
    ): JsonResponse {
        $this->addProjectIfHeaderExist($request);
        $form = $this->createForm(ProjectType::class, null, ['method' => Request::METHOD_DELETE]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Project $dto */
            $dto = $form->getData();
            /** @var User $user */
            $user = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser() : null;

            $service->delete($dto->project, $user, $id);

            return $this->handleResponse();
        }

        return $this->handleFormError($form);
    }

    /**
     * @SWG\Tag(name="Subscription")
     * @SWG\Parameter(name="Project", in="header", type="string", description="Project name")
     * @SWG\Parameter(name="form", in="body", description="Form", @Model(type=ProjectType::class)))
     * @SWG\Response(
     *     response=200,
     *     description="Order",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="items", type="array", @SWG\Items(ref=@Model(type=\App\Entity\Order::class, groups={"list","public.list","createdAt"}))),
     *         @SWG\Property(property="total", type="integer"),
     *     )
     * )
     *
     * @param int $id
     * @param TokenStorageInterface $tokenStorage
     * @param Request $request
     * @param SubscriptionService $service
     * @return JsonResponse
     * @throws ObjectNotFoundException
     */
    public function getOrderList(
        int $id,
        TokenStorageInterface $tokenStorage,
        Request $request,
        SubscriptionService $service
    ): JsonResponse {
        $this->addProjectIfHeaderExist($request);
        $form = $this->createForm(ProjectType::class, null, ['method' => Request::METHOD_GET]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Project $dto */
            $dto = $form->getData();
            /** @var User $user */
            $user = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser() : null;

            return $this->handleResponse(
                $service->getOrderList($dto->project, $user, $id),
                ['list', 'public.list', 'createdAt']
            );
        }

        return $this->handleFormError($form);
    }
}
