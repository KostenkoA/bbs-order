<?php


namespace App\Component\Payment\Method;

use App\Component\Payment\Fondy\Handler\HandlerAbstract;
use App\Component\Payment\Fondy\Model\AbstractModel;
use App\Component\Payment\Fondy\Request\RequestAbstract;
use App\Component\Payment\Fondy\Response\CheckoutResponse;
use App\Component\Payment\Fondy\Response\NewPaymentResponse;
use App\Component\Payment\Fondy\Response\ResponseAbstract;
use App\Entity\Payment;
use App\Component\Payment\CheckoutPaymentInterface;
use App\Component\Payment\NewPaymentInterface;
use App\Component\Payment\Fondy\FondyException;
use App\Component\Payment\Fondy\ModelBuilder;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ServiceLocator;

class Fondy implements PaymentMethodInterface
{
    public const LOCATOR_ACTION_NEW_PAYMENT = 'new payment';

    public const LOCATOR_ACTION_NEW_PAYMENT_BY_TOKEN = 'new payment by token';

    public const LOCATOR_ACTION_CHECK_PAYMENT = 'check payment';

    public const LOCATOR_TYPE_REQUEST = 'request';

    public const LOCATOR_TYPE_MODEL = 'model';

    public const LOCATOR_TYPE_HANDLER = 'handler';

    /**
     * @var ModelBuilder
     */
    private $builder;

    /**
     * @var ServiceLocator
     */
    private $locator;

    /**
     * Fondy constructor.
     * @param ModelBuilder $builder
     * @param ServiceLocator $locator
     */
    public function __construct(ModelBuilder $builder, ServiceLocator $locator)
    {
        $this->builder = $builder;
        $this->locator = $locator;
    }

    /**
     * @param Payment $paymentEntity
     * @return NewPaymentInterface
     * @throws FondyException
     */
    public function newPayment(Payment $paymentEntity): NewPaymentInterface
    {
        $checkoutModel = $this->builder->buildPaymentModel(
            $this->getModelFromLocator(self::LOCATOR_ACTION_NEW_PAYMENT, self::LOCATOR_TYPE_MODEL),
            $paymentEntity
        );

        /** @var NewPaymentResponse $response */
        $response = $this->sendRequest($checkoutModel, self::LOCATOR_ACTION_NEW_PAYMENT);

        return $response;
    }

    /**
     * @param Payment $paymentEntity
     * @return CheckoutPaymentInterface
     * @throws FondyException
     */
    public function paymentByToken(Payment $paymentEntity): CheckoutPaymentInterface
    {
        $checkoutModel = $this->builder->buildPaymentByToken(
            $this->getModelFromLocator(self::LOCATOR_ACTION_NEW_PAYMENT_BY_TOKEN, self::LOCATOR_TYPE_MODEL),
            $paymentEntity
        );

        /** @var CheckoutResponse $response */
        $response = $this->sendRequest($checkoutModel, self::LOCATOR_ACTION_NEW_PAYMENT_BY_TOKEN);

        return $response;
    }

    /**
     * @param Payment $paymentEntity
     * @return CheckoutPaymentInterface
     * @throws FondyException
     */
    public function checkPayment(Payment $paymentEntity): CheckoutPaymentInterface
    {
        $checkoutModel = $this->builder->buildCheckoutModel(
            $this->getModelFromLocator(self::LOCATOR_ACTION_CHECK_PAYMENT, self::LOCATOR_TYPE_MODEL),
            $paymentEntity
        );

        /** @var CheckoutResponse $response */
        $response = $this->sendRequest($checkoutModel, self::LOCATOR_ACTION_CHECK_PAYMENT);

        return $response;
    }

    public function reversePayment(Payment $paymentEntity): CheckoutPaymentInterface
    {
    }


    /**
     * @param $action
     * @param $type
     * @return mixed
     * @throws FondyException
     */
    private function getModelFromLocator($action, $type)
    {
        $serviceName = sprintf('%s-%s', $action, $type);

        if ($this->locator->has($serviceName)) {
            return $this->locator->get($serviceName);
        }

        throw new FondyException(sprintf('%s model for "%s" action not found', $type, $action));
    }

    /**
     * @param AbstractModel $model
     * @param string $action
     * @return CheckoutResponse
     * @throws FondyException
     */
    private function sendRequest(AbstractModel $model, string $action): ResponseAbstract
    {
        /** @var RequestAbstract $request */
        $request = $this->getModelFromLocator($action, self::LOCATOR_TYPE_REQUEST);

        try {
            $request->setModel($model)->send();
        } catch (GuzzleException $exception) {
            throw new FondyException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return $this->handlerRequest($request, $action);
    }


    /**
     * @param RequestAbstract $request
     * @param string $action
     * @return ResponseAbstract
     * @throws FondyException
     */
    private function handlerRequest(RequestAbstract $request, string $action): ResponseAbstract
    {
        /** @var HandlerAbstract $handler */
        $handler = clone($this->getModelFromLocator($action, self::LOCATOR_TYPE_HANDLER));

        $response = $request->getResponse();

        if (!$response) {
            throw new FondyException('Empty response');
        }

        $handler->setResponseStatusCode($response->getStatusCode());
        $handler->setResponseContent($response->getBody()->getContents());

        return $handler->handle();
    }
}
