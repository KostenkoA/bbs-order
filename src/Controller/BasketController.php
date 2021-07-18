<?php

namespace App\Controller;

use App\Controller\Common\ProjectHeaderTrait;
use App\Controller\Common\ResponseHandlerTrait;
use App\Form\BasketType;
use App\Service\BasketService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use App\DTO\BasketChecked\BasketChecked;

class BasketController extends AbstractController
{
    use ResponseHandlerTrait;
    use ProjectHeaderTrait;

    /**
     * @SWG\Tag(name="Basket")
     *
     * @SWG\Parameter(name="Project", in="header", type="string", description="Project name")
     * @SWG\Parameter(in="body", name="Basket form params", @Model(type=BasketType::class))
     * @SWG\Response(response="200", description="Get basket with bonuses", @Model(type=BasketChecked::class, groups={"check"}))
     *
     * @param Request $request
     * @param BasketService $basketService
     * @return JsonResponse
     */
    public function checkBasket(Request $request, BasketService $basketService): JsonResponse
    {
        $this->addProjectIfHeaderExist($request);

        $form = $this->createForm(BasketType::class, null, ['method' => 'POST']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->handleResponse($basketService->checkByBasket($form->getData()), ['check'], Response::HTTP_OK);
        }

        return $this->handleFormError($form);
    }
}
