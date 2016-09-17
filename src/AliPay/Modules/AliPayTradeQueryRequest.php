<?php
namespace Pay\AliPay\Modules;

class AliPayTradeQueryRequest extends AliPayRequest
{
    const METHOD = 'alipay.trade.query';

    /**
     * 商户网站唯一订单号
     * 订单支付时传入的商户订单号,和支付宝交易号不能同时为空。 trade_no,out_trade_no如果同时存在优先取trade_no
     * @var string
     */
    private $outTradeNo = '';

    /**
     * 该交易在支付宝系统中的交易流水号。最长64位
     * 支付宝交易号，和商户订单号不能同时为空
     * @var string
     */
    private $tradeNo = '';

    /**
     * 详见应用授权概述
     * @var string
     */
    private $appAuthToken = '';

    public function getMethod()
    {
        return self::METHOD;
    }

    /**
     * @return string
     */
    public function getBizContent()
    {
        $data = [];
        if ('' != $this->getOutTradeNo()) {
            $data['out_trade_no'] = $this->getOutTradeNo();
        }

        if ('' != $this->getTradeNo()) {
            $data['trade_no'] = $this->getTradeNo();
        }

        return json_encode($data);
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
    public function getAppAuthToken()
    {
        return $this->appAuthToken;
    }

    /**
     * @param string $appAuthToken
     */
    public function setAppAuthToken($appAuthToken)
    {
        $this->appAuthToken = $appAuthToken;
    }
}
