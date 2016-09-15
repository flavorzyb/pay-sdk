<?php
namespace Pay\WxPay\Modules;

/**
 * Class WxPayTradeState
 * @package Pay\WxPay\Modules
 */
class WxPayTradeState
{
    /**
     * SUCCESS—支付成功
    REFUND—转入退款
    NOTPAY—未支付
    CLOSED—已关闭
    REVOKED—已撤销（刷卡支付）
    USERPAYING--用户支付中
    PAYERROR--支付失败(其他原因，如银行返回失败)
     */
    const SUCCESS = 'SUCCESS';
    const REFUND = 'REFUND';
    const NOTPAY = 'NOTPAY';
    const CLOSED = 'CLOSED';
    const REVOKED = 'REVOKED';
    const USERPAYING = 'USERPAYING';
    const PAYERROR = 'PAYERROR';
    const OTHERS = 'OTHERS';

    private $value = self::SUCCESS;

    public function __construct($value)
    {
        $this->setValue($value);
    }

    /**
     * get value
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    protected function setValue($value)
    {
        switch ($value) {
            case self::SUCCESS:
            case self::REFUND:
            case self::NOTPAY:
            case self::CLOSED:
            case self::REVOKED:
            case self::USERPAYING:
            case self::PAYERROR:
                $this->value = $value;
                break;
            default:
                $this->value = self::OTHERS;
        }
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return self::SUCCESS == $this->value;
    }

    public function isRefund()
    {
        return self::REFUND == $this->value;
    }

    public function isNotPay()
    {
        return self::NOTPAY == $this->value;
    }

    public function isClosed()
    {
        return self::CLOSED == $this->value;
    }

    public function isRevoked()
    {
        return self::REVOKED == $this->value;
    }

    public function isUserPaying()
    {
        return self::USERPAYING == $this->value;
    }

    public function isPayError()
    {
        return self::PAYERROR == $this->value;
    }

    public function isOthers()
    {
        return self::OTHERS == $this->value;
    }
}
