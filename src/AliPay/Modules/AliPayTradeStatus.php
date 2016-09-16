<?php
namespace Pay\AliPay\Modules;

class AliPayTradeStatus
{
    /**
     * 交易状态：WAIT_BUYER_PAY（交易创建，等待买家付款）、TRADE_CLOSED（未付款交易超时关闭，或支付完成后全额退款）、
     * TRADE_SUCCESS（交易支付成功）、TRADE_FINISHED（交易结束，不可退款）
     */
    const WAIT_BUYER_PAY = 'WAIT_BUYER_PAY';
    const TRADE_CLOSED = 'TRADE_CLOSED';
    const TRADE_SUCCESS = 'TRADE_SUCCESS';
    const TRADE_FINISHED = 'TRADE_FINISHED';
    const OTHERS = 'OTHERS';

    private $value = self::TRADE_SUCCESS;

    public function __construct($value)
    {
        $this->setValue($value);
    }

    /**
     * @param string $value
     */
    protected function setValue($value)
    {
        switch ($value) {
            case self::WAIT_BUYER_PAY:
            case self::TRADE_CLOSED:
            case self::TRADE_SUCCESS:
            case self::TRADE_FINISHED:
                break;
            default:
                $value = self::OTHERS;
        }

        $this->value = $value;
    }

    /**
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    public function isWaitBuyerPay()
    {
        return self::WAIT_BUYER_PAY == $this->value;
    }

    public function isTradeClosed()
    {
        return self::TRADE_CLOSED == $this->value;
    }

    public function isTradeSuccess()
    {
        return self::TRADE_SUCCESS == $this->value;
    }

    public function isTradeFinished()
    {
        return self::TRADE_FINISHED == $this->value;
    }

    public function isOthers()
    {
        return self::OTHERS == $this->value;
    }
}
