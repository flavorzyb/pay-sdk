<?php
namespace Pay\AliPay\Modules;


class AliPayTradeCloseRequest extends AliPayRequest
{
    const METHOD = 'alipay.trade.close';

    /**
     * 通知地址
     * @var string
     */
    private $notifyUrl = '';

    /**
     * 商户网站唯一订单号
     * @var string
     */
    private $outTradeNo = '';

    /**
     * 针对用户授权接口，获取用户相关数据时，用于标识用户授权关系
     * @var string
     */
    private $appAuthToken = '';

    /**
     * 该交易在支付宝系统中的交易流水号。最长64位
     * @var string
     */
    private $tradeNo = '';

    /**
     * 卖家端自定义的的操作员 ID
     * @var string
     */
    private $operatorId = '';

    /**
     * @return string
     */
    public function getMethod()
    {
        return self::METHOD;
    }

    public function getBizContent()
    {
        $data = [];

        if ('' != $this->getTradeNo()) {
            $data['trade_no'] = $this->getTradeNo();
        }

        if ('' != $this->getOutTradeNo()) {
            $data['out_trade_no'] = $this->getOutTradeNo();
        }

        if ('' != $this->getOperatorId()) {
            $data['operator_id'] = $this->getOperatorId();
        }

        return json_encode($data);
    }

    /**
     * @return string
     */
    public function getNotifyUrl()
    {
        return $this->notifyUrl;
    }

    /**
     * @param string $notifyUrl
     */
    public function setNotifyUrl($notifyUrl)
    {
        $this->notifyUrl = $notifyUrl;
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
    public function getOperatorId()
    {
        return $this->operatorId;
    }

    /**
     * @param string $operatorId
     */
    public function setOperatorId($operatorId)
    {
        $this->operatorId = $operatorId;
    }
}
