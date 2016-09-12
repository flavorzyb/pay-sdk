<?php
namespace Pay\Modules;

/**
 * Class PayNotify
 * 支付回调数据模型
 * @package Pay\Modules
 */
class PayNotify extends AbstractPay
{
    /**
     * 支付平台订单流水号
     * @var string
     */
    private $tradeNo   = '';
    /**
     * 状态
     * @var string
     */
    private $status    = '';

    /**
     * 支付通知状态
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * 支付通知状态
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = trim($status);
    }

    /**
     * 支付平台订单流水号
     * @return string
     */
    public function getTradeNo()
    {
        return $this->tradeNo;
    }

    /**
     * 支付平台订单流水号
     * @param string $tradeNo
     */
    public function setTradeNo($tradeNo)
    {
        $this->tradeNo = trim($tradeNo);
    }
}
