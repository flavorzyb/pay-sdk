<?php
namespace Pay\AliPay\Modules;


class AliPayTradeRefundQueryRequest extends AliPayRequest
{
    const METHOD = 'alipay.trade.fastpay.refund.query';

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
     * 标识一次退款请求，同一笔交易多次退款需要保证唯一，如需部分退款，则此参数必传
     * @var string
     */
    private $outRequestNo = '';

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
        $data['out_request_no'] = $this->getOutRequestNo();

        if ('' != $this->getTradeNo()) {
            $data['trade_no'] = $this->getTradeNo();
        }

        if ('' != $this->getOutTradeNo()) {
            $data['out_trade_no'] = $this->getOutTradeNo();
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
    public function getOutRequestNo()
    {
        return $this->outRequestNo;
    }

    /**
     * @param string $outRequestNo
     */
    public function setOutRequestNo($outRequestNo)
    {
        $this->outRequestNo = $outRequestNo;
    }
}
