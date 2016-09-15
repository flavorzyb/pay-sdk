<?php
namespace Pay\WxPay\Modules;


class WxPayCheckName
{
    const NO_CHECK = 'NO_CHECK';
    const FORCE_CHECK = 'FORCE_CHECK';
    const OPTION_CHECK = 'OPTION_CHECK';

    private $value = self::OPTION_CHECK;

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
            case self::NO_CHECK:
            case self::FORCE_CHECK:
            case self::OPTION_CHECK:
                $this->value = $value;
                break;
            default:
                $this->value = self::OPTION_CHECK;
        }
    }

    /**
     * @return bool
     */
    public function isNoCheck()
    {
        return self::NO_CHECK == $this->value;
    }

    public function isForceCheck()
    {
        return self::FORCE_CHECK == $this->value;
    }

    public function isOptionCheck()
    {
        return self::OPTION_CHECK == $this->value;
    }
}
