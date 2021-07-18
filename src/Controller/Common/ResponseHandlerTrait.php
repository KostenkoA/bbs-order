<?php

namespace App\Controller\Common;

use App\Serializer\ResponseNormalizer;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ResponseHandlerTrait
{
    /**
     * @var ResponseNormalizer
     */
    private $normalizer;

    /**
     * ResponseHandlerTrait constructor.
     *
     * @param ResponseNormalizer $normalizer
     */
    public function __construct(ResponseNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @param $data
     * @param array|null $normalizerGroups
     * @param int|null $status
     * @return JsonResponse
     */
    protected function handleResponse(
        $data = null,
        ?array $normalizerGroups = null,
        ?int $status = null
    ): JsonResponse {
        $normalizerGroups = !empty($normalizerGroups) ? ['groups' => $normalizerGroups] : [];

        $result = $data ? $this->normalizer->normalize(
            $data,
            null,
            array_merge(['enable_max_depth' => true], $normalizerGroups)
        ) : null;


        return new JsonResponse($result, $status ?? Response::HTTP_OK);
    }

    /**
     * @param FormInterface $form
     * @return JsonResponse
     */
    protected function handleFormError(FormInterface $form): JsonResponse
    {
        $errors = [];

        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if ($form->isSubmitted() && !$child->isValid()) {
                $msg = (string)$child->getErrors(true, false);
                $errors[$child->getName()] = $msg;
            }
        }

        return $this->handleError($errors, 'Form validation error');
    }

    /**
     * @param array $errors
     * @param string|null $msg
     * @param int|null $status
     * @return JsonResponse
     */
    protected function handleError(array $errors, ?string $msg = null, ?int $status = null): JsonResponse
    {
        $status = $status ?? Response::HTTP_BAD_REQUEST;
        $msg = $msg ?? '';

        return new JsonResponse(
            [
                'code' => $status,
                'error' => $msg,
                'errors' => $errors,
            ],
            $status
        );
    }
}
