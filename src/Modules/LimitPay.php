<?php
namespace Pay\Modules;

class LimitPay
{
    /**
     * 正常
     */
    const NORMAL = '';
    /**
     * 禁止使用信用卡
     */
    const NO_CREDIT ='no_credit';

    private $value = self::NORMAL;

    public function __construct($value)
    {
        $this->setValue($value);
    }

    /**
     *
     * @param string $value
     */
    protected function setValue($value)
    {
        $value = trim($value);
        if (self::NO_CREDIT == $value) {
            $this->value = self::NO_CREDIT;
        } else {
            $this->value = self::NORMAL;
        }
    }

    /**
     * get value
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * 禁止使用信用卡
     * @return bool
     */
    public function isNoCredit()
    {
        return self::NO_CREDIT == $this->value;
    }
}
