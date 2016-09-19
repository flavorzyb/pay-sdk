<?php
namespace Pay\Modules;

class PayOrderQueryResult extends PayOrderQuery
{
    /**
     * 订单总金额，单位为分
     * @var float
     */
    private $totalAmount = 0;

    /**
     * 实收金额，单位为元，两位小数。
     * @var float
     */
    private $receiptAmount = 0;
    /**
     * @var PayTradeStatus
     */
    private $tradeStatus = null;

    /**
     * PayOrderQueryResult constructor.
     */
    public function __construct()
    {
        $this->tradeStatus = PayTradeStatus::createOthersStatus();
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
     * @return PayTradeStatus
     */
    public function getTradeStatus()
    {
        return $this->tradeStatus;
    }

    /**
     * @param PayTradeStatus $tradeStatus
     */
    public function setTradeStatus(PayTradeStatus $tradeStatus)
    {
        $this->tradeStatus = $tradeStatus;
    }
}
