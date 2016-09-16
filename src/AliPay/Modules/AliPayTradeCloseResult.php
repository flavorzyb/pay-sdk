<?php
namespace Pay\AliPay\Modules;


class AliPayTradeCloseResult extends AliPayResult
{
    /**
     * 支付宝交易号
     * @var string
     */
    private $tradeNo = '';
    /**
     * 创建交易传入的商户订单号
     * @var string
     */
    private $outTradeNo = '';

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
    public function getOutTradeNo()
    {
        return $this->outTradeNo;
    }

    /**
     * @param string $outTradeNo
     */
    public function setOutTradeNo($outTradeNo)
    {
        $this->outTradeNo = $outTradeNo;
    }
}
