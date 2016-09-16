<?php
namespace Pay\AliPay\Modules;

class AliPayTradeQueryResult extends AliPayResult
{
    /**
     * 支付宝交易号
     * @var string
     */
    private $tradeNo = '';

    /**
     * 商家订单号
     * @var string
     */
    private $outTradeNo = '';

    /**
     * 买家支付宝账号
     * @var string
     */
    private $buyerLogonId = '';

    /**
     * 交易状态：WAIT_BUYER_PAY（交易创建，等待买家付款）、
     * TRADE_CLOSED（未付款交易超时关闭，或支付完成后全额退款）、
     * TRADE_SUCCESS（交易支付成功）、
     * TRADE_FINISHED（交易结束，不可退款）
     * @var AliPayTradeStatus
     */
    private $tradeStatus = null;

    /**
     * 交易的订单金额，单位为元，两位小数
     * @var float
     */
    private $totalAmount = 0;

    /**
     * 实收金额，单位为元，两位小数
     * @var float
     */
    private $receiptAmount = 0;

    /**
     * 买家实付金额，单位为元，两位小数。
     * @var float
     */
    private $buyerPayAmount = 0;

    /**
     * 积分支付的金额，单位为元，两位小数
     * @var float
     */
    private $pointAmount = 0;

    /**
     * 交易中用户支付的可开具发票的金额，单位为元，两位小数。
     * @var float
     */
    private $invoiceAmount = 0;

    /**
     * 本次交易打款给卖家的时间
     * @var string
     */
    private $sendPayDate = '';

    /**
     * 支付宝店铺编号
     * @var string
     */
    private $alipayStoreId = '';

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
     * 本次交易支付所使用的单品券优惠的商品优惠信息
     * @var string
     */
    private $discountGoodsDetail = '';

    /**
     * 行业特殊信息（例如在医保卡支付业务中，向用户返回医疗信息）。
     * @var string
     */
    private $industrySepcDetail = '';

    /**
     * 交易支付使用的资金渠道
     * @var array of AliPayTradeFundBill
     */
    private $fundBillList = [];

    /**
     * AliPayTradeQueryResult constructor.
     */
    public function __construct()
    {
        $this->tradeStatus = new AliPayTradeStatus(AliPayTradeStatus::OTHERS);
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
     * @return string
     */
    public function getSendPayDate()
    {
        return $this->sendPayDate;
    }

    /**
     * @param string $sendPayDate
     */
    public function setSendPayDate($sendPayDate)
    {
        $this->sendPayDate = $sendPayDate;
    }

    /**
     * @return string
     */
    public function getAlipayStoreId()
    {
        return $this->alipayStoreId;
    }

    /**
     * @param string $alipayStoreId
     */
    public function setAlipayStoreId($alipayStoreId)
    {
        $this->alipayStoreId = $alipayStoreId;
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
     * @return string
     */
    public function getDiscountGoodsDetail()
    {
        return $this->discountGoodsDetail;
    }

    /**
     * @param string $discountGoodsDetail
     */
    public function setDiscountGoodsDetail($discountGoodsDetail)
    {
        $this->discountGoodsDetail = $discountGoodsDetail;
    }

    /**
     * @return string
     */
    public function getIndustrySepcDetail()
    {
        return $this->industrySepcDetail;
    }

    /**
     * @param string $industrySepcDetail
     */
    public function setIndustrySepcDetail($industrySepcDetail)
    {
        $this->industrySepcDetail = $industrySepcDetail;
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
