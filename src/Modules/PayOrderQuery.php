<?php
namespace Pay\Modules;

class PayOrderQuery
{
    /**
     * 支付平台的订单号，优先使用
     * @var string
     */
    private $tradeNo = '';
    /**
     * 订单号
     * @var string
     */
    private $orderId = '';

    /**
     * @return string
     */
    public function getTradeNo()
    {
        return $this->tradeNo;
    }

    /**
     * @param string $tradeNo
     */
    public function setTradeNo($tradeNo)
    {
        $this->tradeNo = $tradeNo;
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }
}
