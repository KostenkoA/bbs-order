<?php

namespace App\Controller;

use App\Controller\Common\ProjectHeaderTrait;
use App\Controller\Common\ResponseHandlerTrait;
use App\DTO\Project;
use App\Exception\ObjectNotFoundException;
use App\Form\ProjectType;
use App\Security\User;
use App\Service\CardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;

class CardController extends AbstractController
{
    use ResponseHandlerTrait;
    use ProjectHeaderTrait;

    /**
     * @SWG\Tag(name="Card")
     * @SWG\Parameter(name="Project", in="header", type="string", description="Project name")
     * @SWG\Parameter(name="form", in="body", description="Form", @Model(type=ProjectType::class)))
     * @SWG\Response(
     *     response=200,
     *     description="Subscription",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(property="items", type="array", @SWG\Items(ref=@Model(type=\App\Entity\Card::class, groups={"list","public.info"}))),
     *         @SWG\Property(property="total", type="integer"),
     *     )
     * )
     *
     * @param TokenStorageInterface $tokenStorage
     * @param Request $request
     * @param CardService $service
     * @return JsonResponse
     */
    public function getList(
        TokenStorageInterface $tokenStorage,
        Request $request,
        CardService $service
    ): JsonResponse {
        $this->addProjectIfHeaderExist($request);
        $form = $this->createForm(ProjectType::class, null, ['method' => Request::METHOD_GET]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Project $dto */
            $dto = $form->getData();
            /** @var User $user */
            $user = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser() : null;

            return $this->handleResponse($service->getAll($dto->project, $user), ['list', 'public.info']);
        }

        return $this->handleFormError($form);
    }

    /**
     * @SWG\Tag(name="Card")
     * @SWG\Parameter(name="Project", in="header", type="string", description="Project name")
     * @SWG\Parameter(name="form", in="body", description="Form", @Model(type=ProjectType::class)))
     * @SWG\Response(response=200, description="ok", @Model(type=\App\Entity\Subscription::class, groups={"created"}))
     *
     * @param TokenStorageInterface $tokenStorage
     * @param Request $request
     * @param CardService $service
     * @return JsonResponse
     */
    public function create(
        TokenStorageInterface $tokenStorage,
        Request $request,
        CardService $service
    ): JsonResponse {
        $this->addProjectIfHeaderExist($request);
        $form = $this->createForm(ProjectType::class, null, ['method' => Request::METHOD_POST]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Project $dto */
            $dto = $form->getData();
            /** @var User $user */
            $user = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser() : null;

            return $this->handleResponse($service->create($dto->project, $user), ['created'], Response::HTTP_CREATED);
        }

        return $this->handleFormError($form);
    }

    /**
     * @SWG\Tag(name="Card")
     * @SWG\Parameter(name="Project", in="header", type="string", description="Project name")
     * @SWG\Parameter(name="form", in="body", description="Form", @Model(type=ProjectType::class)))
     * @SWG\Response(response=200, description="ok")
     *
     * @param string $hash
     * @param TokenStorageInterface $tokenStorage
     * @param Request $request
     * @param CardService $service
     * @return JsonResponse
     * @throws ObjectNotFoundException
     */
    public function delete(
        string $hash,
        TokenStorageInterface $tokenStorage,
        Request $request,
        CardService $service
    ): JsonResponse {
        $this->addProjectIfHeaderExist($request);
        $form = $this->createForm(ProjectType::class, null, ['method' => Request::METHOD_DELETE]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Project $dto */
            $dto = $form->getData();
            /** @var User $user */
            $user = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser() : null;

            $service->delete($dto->project, $user, $hash);

            return $this->handleResponse();
        }

        return $this->handleFormError($form);
    }
}
