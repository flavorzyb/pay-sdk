<?php
namespace Pay;

use Pay\Modules\PayOrder;
use Simple\Log\Writer;

abstract class PayAbstract
{
    /**
     * @var Writer
     */
    protected $logWriter = null;

    /**
     * PayAbstract constructor.
     * @param Writer $logWriter
     */
    public function __construct(Writer $logWriter)
    {
        $this->logWriter = $logWriter;
    }

    /**
     * @return Writer
     */
    protected function getLogWriter()
    {
        return $this->logWriter;
    }

    /**
     * 支付
     * @param PayOrder $payOrder
     * @param string $ip
     * @return bool
     */
    public function pay(PayOrder $payOrder, $ip)
    {
        if ('' == $payOrder->getOrderId()) {
            $this->getLogWriter()->error("订单ID不能为空");
            return false;
        }

        if ('' == $payOrder->getGoodsName()) {
            $this->getLogWriter()->error("商品名不能为空");
            return false;
        }

        if (0.01 > $payOrder->getPayAmount()) {
            $this->getLogWriter()->error("支付款不能为0");
            return false;
        }

        if (false !== stripos($payOrder->getExtra(), '|')) {
            $this->getLogWriter()->error("扩展字段不能包含|字符");
            return false;
        }

        return true;
    }

    public function orderQuery()
    {
    }

    public function closeOrder()
    {
    }

    public function refund()
    {
    }

    public function refundQuery()
    {
    }

    public function parseNotify($string, $ip)
    {
    }

    public function parsePayReturnResult()
    {
    }
}
