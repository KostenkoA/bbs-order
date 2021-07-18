<?php

namespace App\Component\Payment\Fondy\Response;

use App\Component\Payment\Model\Card;
use App\Component\Payment\CardInterface;
use App\Entity\PaymentStatusInterface;
use App\Component\Payment\CheckoutPaymentInterface;
use Symfony\Component\Serializer\Annotation as Serializer;

class CheckoutResponse extends ResponseAbstract implements CheckoutPaymentInterface, CardInterface
{
    /**
     * покупка
     */
    public const TRAN_TYPE_BUY = 'purchase';

    /**
     * отмена/возврат
     */
    public const TRAN_TYPE_REVERSE = 'reverse';

    /**
     * Заказ был создан, но клиент еще не ввел платежные реквизиты; необходимо продолжать опрашивать статус заказа
     */
    public const ORDER_STATUS_CREATED = 'created';

    /**
     * Заказ все еще находится в процессе обработки платежным шлюзом; необходимо продолжать опрашивать статус заказа
     */
    public const ORDER_STATUS_PROCESSING = 'processing';

    /**
     * Заказ отклонен платежным шлюзом FONDY, внешней платежной системой или банком-эквайером
     */
    public const ORDER_STATUS_DECLINED = 'declined';

    /**
     * Заказ успешно совершен, средства заблокированы на счету плательщика и вскоре будут зачислены мерчанту;
     * мерчант может оказывать услугу или “отгружать” товар
     */
    public const ORDER_STATUS_APPROVED = 'approved';

    /**
     * Время жизни заказа, указанное в параметре lifetime, истекло
     */
    public const ORDER_STATUS_EXPIRED = 'expired';

    /**
     * Ранее успешная транзакция была полностью или частично отменена.
     * В таком случае параметр reversal_amount имеет не нулевое значение
     */
    public const ORDER_STATUS_REVERSED = 'reversed';

    /**
     * Проверочный код создан, но не вводился клиентом
     */
    public const VERIFICATION_STATUS_CREATED = 'created';

    /**
     * Карта успешно верифицирована по коду
     */
    public const VERIFICATION_STATUS_VERIFIED = 'verified';

    /**
     * Введен неверный код верификации, но еще не исчерпан лимит попыток
     */
    public const VERIFICATION_STATUS_INCORRECT = 'incorrect';

    /**
     * Исчерпан лимит неверных попыток ввода проверочного кода
     */
    public const VERIFICATION_STATUS_FAILED = 'failed';

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $order_id;

    /**
     * @var integer
     * @Serializer\Groups({"save"})
     */
    public $amount;

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $order_status;

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $tran_type;

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $sender_cell_phone;

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $sender_account;

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $masked_card;

    /**
     * @var integer
     * @Serializer\Groups({"save"})
     */
    public $card_bin;

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $card_type;

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $rrn;

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $approval_code;

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
     * @var integer
     * @Serializer\Groups({"save"})
     */
    public $reversal_amount;

    /**
     * @var integer
     * @Serializer\Groups({"save"})
     */
    public $settlement_amount;

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $settlement_currency;

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $order_time;

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $settlement_date;

    /**
     * @var integer
     * @Serializer\Groups({"save"})
     */
    public $eci;

    /**
     * @var integer
     * @Serializer\Groups({"save"})
     */
    public $fee;

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $payment_system;

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $sender_email;

    /**
     * @var integer
     * @Serializer\Groups({"save"})
     */
    public $payment_id;

    /**
     * @var integer
     * @Serializer\Groups({"save"})
     */
    public $actual_amount;

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $actual_currency;

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $product_id;

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $merchant_data;

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $verification_status;

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $rectoken;

    /**
     * @var string
     * @Serializer\Groups({"save"})
     */
    public $rectoken_lifetime;


    /**
     * @var Card|null
     */
    public $card;

    /**
     * @return int
     */
    public function getPaymentStatus(): int
    {
        $status = null;
        switch ($this->order_status) {
            case self::ORDER_STATUS_CREATED:
                $status = PaymentStatusInterface::STATUS_CREATED;
                break;
            case self::ORDER_STATUS_PROCESSING:
                $status = PaymentStatusInterface::STATUS_PROCESSING;
                break;
            case self::ORDER_STATUS_APPROVED:
                $status = PaymentStatusInterface::STATUS_APPROVED;
                break;
            case self::ORDER_STATUS_DECLINED:
                $status = PaymentStatusInterface::STATUS_DECLINED;
                break;
            case self::ORDER_STATUS_EXPIRED:
                $status = PaymentStatusInterface::STATUS_EXPIRED;
                break;
            case self::ORDER_STATUS_REVERSED:
                $status = PaymentStatusInterface::STATUS_REVERSED;
                break;
            default:
                $status = PaymentStatusInterface::STATUS_UNDEFINED;
        }

        return $status;
    }

    public function getCard(): ?Card
    {
        return $this->card;
    }
}
