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
    public function getLogWriter()
    {
        return $this->logWriter;
    }

    /**
     * 生成支付的url
     * @param PayOrder $payOrder
     * @return string
     */
    abstract protected function _payUrl(PayOrder $payOrder);

    /**
     * 支付
     * @param PayOrder $payOrder
     */
    public function pay(PayOrder $payOrder)
    {
        $url = $this->payUrl($payOrder);
        $this->getLogWriter()->info("pay url:".$url);
        if ('' != $url) {
            header('Location:' . $url);
            exit();
        }
    }

    /**
     * 支付URL
     * @param PayOrder $payOrder
     * @return string
     */
    public function payUrl(PayOrder $payOrder)
    {
        if (!$this->check($payOrder)) {
            return '';
        }

        return $this->_payUrl($payOrder);
    }

    /**
     * 检查
     * @param PayOrder $payOrder
     * @return bool
     */
    protected function check(PayOrder $payOrder)
    {
        if ('' == $payOrder->getOrderId()) {
            $this->getLogWriter()->error("订单ID不能为空");
            return false;
        }

        if ('' == $payOrder->getGoodsName()) {
            $this->getLogWriter()->error("商品名不能为空");
            return false;
        }

        if (0 >= $payOrder->getPayAmount()) {
            $this->getLogWriter()->error("支付款不能为0");
            return false;
        }

        if (false !== stripos($payOrder->getExtra(), '|')) {
            $this->getLogWriter()->error("扩展字段不能包含|字符");
            return false;
        }

        return true;
    }
}
