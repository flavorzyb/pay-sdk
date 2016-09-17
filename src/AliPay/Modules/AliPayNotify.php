<?php
namespace Pay\AliPay\Modules;

class AliPayNotify
{
    /**
     * 通知时间
     * 格式为yyyy-MM-dd HH:mm:ss
     * @var string
     */
    private $notifyTime = '';

    /**
     * 通知类型
     * @var string
     */
    private $notifyType = '';

    /**
     * 通知校验ID
     * @var string
     */
    private $notifyId = '';

    /**
     * 签名算法类型，目前支持RSA
     * @var string
     */
    private $signType = '';

    /**
     * 签名
     * 请参考异步返回结果的验签
     * @var string
     */
    private $sign = '';

    /**
     * 支付宝交易凭证号
     * @var string
     */
    private $tradeNo = '';

    /**
     * 支付宝分配给开发者的应用Id
     * @var string
     */
    private $appId = '';

    /**
     * 商户订单号
     * 原支付请求的商户订单号
     * @var string
     */
    private $outTradeNo = '';

    /**
     * 商户业务ID，主要是退款通知中返回退款申请的流水号
     * @var string
     */
    private $outBizNo = '';

    /**
     * 买家支付宝账号对应的支付宝唯一用户号。以2088开头的纯16位数字
     * @var string
     */
    private $buyerId = '';

    /**
     * 买家支付宝账号
     * @var string
     */
    private $buyerLogonId = '';

    /**
     * 卖家支付宝用户号
     * @var string
     */
    private $sellerId = '';

    /**
     * 卖家支付宝账号
     *
     * @var string
     */
    private $sellerEmail = '';

    /**
     * 交易状态
     * @var AliPayTradeStatus
     */
    private $tradeStatus = null;

    /**
     * 订单金额
     * 本次交易支付的订单金额，单位为人民币（元）
     * @var float
     */
    private $totalAmount = 0;

    /**
     * 实收金额
     * 商家在交易中实际收到的款项，单位为元
     * @var float
     */
    private $receiptAmount = 0;

    /**
     * 开票金额
     * 用户在交易中支付的可开发票的金额
     * @var float
     */
    private $invoiceAmount = 0;

    /**
     * 付款金额
     * 用户在交易中支付的金额
     * @var float
     */
    private $buyerPayAmount = 0;

    /**
     * 集分宝金额
     * 使用集分宝支付的金额
     * @var float
     */
    private $pointAmount = 0;

    /**
     * 总退款金额
     * 退款通知中，返回总退款金额，单位为元，支持两位小数
     * @var float
     */
    private $refundFee = 0;
    /**
     * 订单标题
     * 商品的标题/交易标题/订单标题/订单关键字等，是请求时对应的参数，原样通知回来
     * @var string
     */
    private $subject = '';

    /**
     * 该订单的备注、描述、明细等。对应请求时的body参数，原样通知回来
     * @var string
     */
    private $body = '';

    /**
     * 交易创建时间
     * 该笔交易创建的时间。格式为yyyy-MM-dd HH:mm:ss
     * @var string
     */
    private $gmtCreate = '';

    /**
     * 交易付款时间
     * 该笔交易的买家付款时间。格式为yyyy-MM-dd HH:mm:ss
     * @var string
     */
    private $gmtPayment = '';

    /**
     * 交易退款时间
     * 该笔交易的退款时间。格式为yyyy-MM-dd HH:mm:ss.S
     * @var string
     */
    private $gmtRefund = '';

    /**
     * 交易结束时间
     * 该笔交易结束时间。格式为yyyy-MM-dd HH:mm:ss
     * @var string
     */
    private $gmtClose = '';

    /**
     * 支付金额信息
     * 支付成功的各个渠道金额信息，详见资金明细信息说明
     * @var array
     */
    private $fundBillList = [];
    /**
     * AliPayNotify constructor.
     */
    public function __construct()
    {
        $this->tradeStatus = new AliPayTradeStatus(AliPayTradeStatus::OTHERS);
    }

    /**
     * @return string
     */
    public function getNotifyTime()
    {
        return $this->notifyTime;
    }

    /**
     * @param string $notifyTime
     */
    public function setNotifyTime($notifyTime)
    {
        $this->notifyTime = $notifyTime;
    }

    /**
     * @return string
     */
    public function getNotifyType()
    {
        return $this->notifyType;
    }

    /**
     * @param string $notifyType
     */
    public function setNotifyType($notifyType)
    {
        $this->notifyType = $notifyType;
    }

    /**
     * @return string
     */
    public function getNotifyId()
    {
        return $this->notifyId;
    }

    /**
     * @param string $notifyId
     */
    public function setNotifyId($notifyId)
    {
        $this->notifyId = $notifyId;
    }

    /**
     * @return string
     */
    public function getSignType()
    {
        return $this->signType;
    }

    /**
     * @param string $signType
     */
    public function setSignType($signType)
    {
        $this->signType = $signType;
    }

    /**
     * @return string
     */
    public function getSign()
    {
        return $this->sign;
    }

    /**
     * @param string $sign
     */
    public function setSign($sign)
    {
        $this->sign = $sign;
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
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param string $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
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
    public function getOutBizNo()
    {
        return $this->outBizNo;
    }

    /**
     * @param string $outBizNo
     */
    public function setOutBizNo($outBizNo)
    {
        $this->outBizNo = $outBizNo;
    }

    /**
     * @return string
     */
    public function getBuyerId()
    {
        return $this->buyerId;
    }

    /**
     * @param string $buyerId
     */
    public function setBuyerId($buyerId)
    {
        $this->buyerId = $buyerId;
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
    public function getSellerId()
    {
        return $this->sellerId;
    }

    /**
     * @param string $sellerId
     */
    public function setSellerId($sellerId)
    {
        $this->sellerId = $sellerId;
    }

    /**
     * @return string
     */
    public function getSellerEmail()
    {
        return $this->sellerEmail;
    }

    /**
     * @param string $sellerEmail
     */
    public function setSellerEmail($sellerEmail)
    {
        $this->sellerEmail = $sellerEmail;
    }

    /**
     * @return AliPayTradeStatus
     */
    public function getTradeStatus()
    {
        return $this->tradeStatus;
    }

    /**
     * @param AliPayTradeStatus $tradeStatus
     */
    public function setTradeStatus($tradeStatus)
    {
        $this->tradeStatus = $tradeStatus;
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
    public function getReceiptAmount()
    {
        return $this->receiptAmount;
    }

    /**
     * @param float $receiptAmount
     */
    public function setReceiptAmount($receiptAmount)
    {
        $this->receiptAmount = $receiptAmount;
    }

    /**
     * @return float
     */
    public function getInvoiceAmount()
    {
        return $this->invoiceAmount;
    }

    /**
     * @param float $invoiceAmount
     */
    public function setInvoiceAmount($invoiceAmount)
    {
        $this->invoiceAmount = $invoiceAmount;
    }

    /**
     * @return float
     */
    public function getBuyerPayAmount()
    {
        return $this->buyerPayAmount;
    }

    /**
     * @param float $buyerPayAmount
     */
    public function setBuyerPayAmount($buyerPayAmount)
    {
        $this->buyerPayAmount = $buyerPayAmount;
    }

    /**
     * @return float
     */
    public function getPointAmount()
    {
        return $this->pointAmount;
    }

    /**
     * @param float $pointAmount
     */
    public function setPointAmount($pointAmount)
    {
        $this->pointAmount = $pointAmount;
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
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getGmtCreate()
    {
        return $this->gmtCreate;
    }

    /**
     * @param string $gmtCreate
     */
    public function setGmtCreate($gmtCreate)
    {
        $this->gmtCreate = $gmtCreate;
    }

    /**
     * @return string
     */
    public function getGmtPayment()
    {
        return $this->gmtPayment;
    }

    /**
     * @param string $gmtPayment
     */
    public function setGmtPayment($gmtPayment)
    {
        $this->gmtPayment = $gmtPayment;
    }

    /**
     * @return string
     */
    public function getGmtRefund()
    {
        return $this->gmtRefund;
    }

    /**
     * @param string $gmtRefund
     */
    public function setGmtRefund($gmtRefund)
    {
        $this->gmtRefund = $gmtRefund;
    }

    /**
     * @return string
     */
    public function getGmtClose()
    {
        return $this->gmtClose;
    }

    /**
     * @param string $gmtClose
     */
    public function setGmtClose($gmtClose)
    {
        $this->gmtClose = $gmtClose;
    }

    /**
     * @return array
     */
    public function getFundBillList()
    {
        return $this->fundBillList;
    }

    /**
     * @param array $fundBillList
     */
    public function setFundBillList($fundBillList)
    {
        $this->fundBillList = $fundBillList;
    }
}
