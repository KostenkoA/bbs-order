<?php

namespace App\Component\Payment\Fondy\Response;

use App\Entity\PaymentStatusInterface;
use App\Component\Payment\CheckoutPaymentInterface;
use Symfony\Component\Serializer\Annotation as Serializer;

class ReverseResponse extends ResponseAbstract implements CheckoutPaymentInterface
{
    /**
     * возврат был создан, но еще не обработан
     */
    public const REVERSE_STATUS_CREATED = 'created';

    /**
     * возврат отклонен платежным шлюзом FONDY, внешней платежной системой или банком-эквайером
     */
    public const REVERSE_STATUS_DECLINED = 'declined';

    /**
     * возврат успешно совершен
     */
    public const REVERSE_STATUS_APPROVED = 'approved';

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $order_id;

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $reverse_status;

    /**
     * @var integer
     * @Serializer\Groups({"save"})
     */
    public $response_code;

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $response_description;

    /**
     * @return int
     */
    public function getPaymentStatus(): int
    {
        $status = null;
        switch ($this->reverse_status) {
            case self::REVERSE_STATUS_CREATED:
                $status = PaymentStatusInterface::STATUS_CREATED;
                break;
            case self::REVERSE_STATUS_DECLINED:
                $status = PaymentStatusInterface::STATUS_PROCESSING;
                break;
            case self::REVERSE_STATUS_APPROVED:
                $status = PaymentStatusInterface::STATUS_APPROVED;
                break;
        }

        return $status;
    }
}
