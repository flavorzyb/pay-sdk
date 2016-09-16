<?php
namespace Pay\AliPay\Modules;

class AliPayTradeFundBill
{
    /**
     * 交易使用的资金渠道
     * @var string
     */
    private $fundChannel = '';

    /**
     * 该支付工具类型所使用的金额
     * @var float
     */
    private $amount = 0;

    /**
     * 渠道实际付款金额
     * @var float
     */
    private $realAmount = 0;

    /**
     * @return string
     */
    public function getFundChannel()
    {
        return $this->fundChannel;
    }

    /**
     * @param string $fundChannel
     */
    public function setFundChannel($fundChannel)
    {
        $this->fundChannel = $fundChannel;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return float
     */
    public function getRealAmount()
    {
        return $this->realAmount;
    }

    /**
     * @param float $realAmount
     */
    public function setRealAmount($realAmount)
    {
        $this->realAmount = $realAmount;
    }
}
