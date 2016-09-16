<?php
namespace Pay\AliPay\Modules;


class AliPayTradeRefundRequest extends AliPayRequest
{
    const METHOD = 'alipay.trade.refund';

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
     * 需要退款的金额，该金额不能大于订单金额,单位为元，支持两位小数
     * @var float
     */
    private $refundAmount = 0;

    /**
     * 退款的原因说明
     * @var string
     */
    private $refundReason = '';

    /**
     * 标识一次退款请求，同一笔交易多次退款需要保证唯一，如需部分退款，则此参数必传
     * @var string
     */
    private $outRequestNo = '';

    /**
     * 商户门店编号
     * @var string
     */
    private $storeId = '';

    /**
     * 商户机具终端编号
     * @var string
     */
    private $terminalId = '';

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
        $data['refund_amount'] = $this->getRefundAmount();

        if ('' != $this->getTradeNo()) {
            $data['trade_no'] = $this->getTradeNo();
        }

        if ('' != $this->getOutTradeNo()) {
            $data['out_trade_no'] = $this->getOutTradeNo();
        }

        if ('' != $this->getRefundReason()) {
            $data['refund_reason'] = $this->getRefundReason();
        }

        if ('' != $this->getOutRequestNo()) {
            $data['out_request_no'] = $this->getOutRequestNo();
        }

        if ('' != $this->getStoreId()) {
            $data['store_id'] = $this->getStoreId();
        }

        if ('' != $this->getTerminalId()) {
            $data['terminal_id'] = $this->getTerminalId();
        }

        if ('' != $this->getOperatorId()) {
            $data['operator_id'] = $this->getOperatorId();
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

    /**
     * @return float
     */
    public function getRefundAmount()
    {
        return $this->refundAmount;
    }

    /**
     * @param float $refundAmount
     */
    public function setRefundAmount($refundAmount)
    {
        $this->refundAmount = $refundAmount;
    }

    /**
     * @return string
     */
    public function getRefundReason()
    {
        return $this->refundReason;
    }

    /**
     * @param string $refundReason
     */
    public function setRefundReason($refundReason)
    {
        $this->refundReason = $refundReason;
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

    /**
     * @return string
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * @param string $storeId
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
    }

    /**
     * @return string
     */
    public function getTerminalId()
    {
        return $this->terminalId;
    }

    /**
     * @param string $terminalId
     */
    public function setTerminalId($terminalId)
    {
        $this->terminalId = $terminalId;
    }
}
