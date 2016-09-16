<?php
namespace Pay\AliPay\Modules;


class AliPayTradeRefundResult extends AliPayResult
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
     * 用户的登录id
     * @var string
     */
    private $buyerLogonId = '';

    /**
     * 本次退款是否发生了资金变化
     * @var string
     */
    private $fundChange = '';

    /**
     * 退款总金额
     * @var float
     */
    private $refundFee = 0;

    /**
     * 退款支付时间
     * @var string
     */
    private $gmtRefundPay = '';

    /**
     * 请求交易支付中的商户店铺的名称
     * @var string
     */
    private $storeName = '';

    /**
     * 买家在支付宝的用户id
     * @var string
     */
    private $buyerUserId = '';

    /**
     * 本次商户实际退回金额
     * @var float
     */
    private $sendBackFee = 0;

    /**
     * 退款使用的资金渠道
     * @var array
     */
    private $refundDetailItemList = [];

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
    public function getBuyerLogonId()
    {
        return $this->buyerLogonId;
    }

    /**
     * @param string $buyerLogonId
     */
    public function setBuyerLogonId($buyerLogonId)
    {
        $this->buyerLogonId = $buyerLogonId;
    }

    /**
     * @return string
     */
    public function getFundChange()
    {
        return $this->fundChange;
    }

    /**
     * @param string $fundChange
     */
    public function setFundChange($fundChange)
    {
        $this->fundChange = $fundChange;
    }

    /**
     * @return float
     */
    public function getRefundFee()
    {
        return $this->refundFee;
    }

    /**
     * @param float $refundFee
     */
    public function setRefundFee($refundFee)
    {
        $this->refundFee = $refundFee;
    }

    /**
     * @return string
     */
    public function getGmtRefundPay()
    {
        return $this->gmtRefundPay;
    }

    /**
     * @param string $gmtRefundPay
     */
    public function setGmtRefundPay($gmtRefundPay)
    {
        $this->gmtRefundPay = $gmtRefundPay;
    }

    /**
     * @return string
     */
    public function getStoreName()
    {
        return $this->storeName;
    }

    /**
     * @param string $storeName
     */
    public function setStoreName($storeName)
    {
        $this->storeName = $storeName;
    }

    /**
     * @return string
     */
    public function getBuyerUserId()
    {
        return $this->buyerUserId;
    }

    /**
     * @param string $buyerUserId
     */
    public function setBuyerUserId($buyerUserId)
    {
        $this->buyerUserId = $buyerUserId;
    }

    /**
     * @return float
     */
    public function getSendBackFee()
    {
        return $this->sendBackFee;
    }

    /**
     * @param float $sendBackFee
     */
    public function setSendBackFee($sendBackFee)
    {
        $this->sendBackFee = $sendBackFee;
    }

    /**
     * @return array
     */
    public function getRefundDetailItemList()
    {
        return $this->refundDetailItemList;
    }

    /**
     * @param array $refundDetailItemList
     */
    public function setRefundDetailItemList($refundDetailItemList)
    {
        $this->refundDetailItemList = $refundDetailItemList;
    }
}
