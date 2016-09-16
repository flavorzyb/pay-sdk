<?php
namespace Pay\AliPay\Modules;


class AliPayTradeRefundQueryResult extends AliPayResult
{
    /**
     * 商户网站唯一订单号
     * @var string
     */
    private $outTradeNo = '';

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
     * 发起退款时，传入的退款原因
     * @var string
     */
    private $refundReason = '';

    /**
     * 该笔退款所对应的交易的订单金额
     * @var float
     */
    private $totalAmount = 0;

    /**
     * 本次退款请求，对应的退款金额
     * @var float
     */
    private $refundAmount = 0;

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
     * @return float
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * @param float $totalAmount
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
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
}
