<?php
namespace Pay\Modules;

class PayTradeStatus
{
    const SUCCESS = 'SUCCESS';
    const CLOSED = 'CLOSED';
    const NOTPAY = 'NOTPAY';
    const FINISHED = 'FINISHED';
    const OTHERS = 'OTHERS';

    private $value = self::OTHERS;

    /**
     * PayTradeStatus constructor.
     * @param string $value
     */
    private function __construct($value)
    {
        $this->setValue($value);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    private function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return PayTradeStatus
     */
    public static function createSuccessStatus()
    {
        return new self(self::SUCCESS);
    }

    public static function createClosedStatus()
    {
        return new self(self::CLOSED);
    }

    public static function createNotPayStatus()
    {
        return new self(self::NOTPAY);
    }

    public static function createFinishStatus()
    {
        return new self(self::FINISHED);
    }

    public static function createOthersStatus()
    {
        return new self(self::OTHERS);
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return self::SUCCESS == $this->value;
    }

    /**
     * @return bool
     */
    public function isClosed()
    {
        return self::CLOSED == $this->value;
    }

    /**
     * @return bool
     */
    public function isNotPay()
    {
        return self::NOTPAY == $this->value;
    }

    /**
     * @return bool
     */
    public function isFinished()
    {
        return self::FINISHED == $this->value;
    }

    /**
     * @return bool
     */
    public function isOthers()
    {
        return self::OTHERS == $this->value;
    }
}
