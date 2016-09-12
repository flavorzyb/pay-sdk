<?php
namespace Pay\WxPay;

class WxPayException extends \Exception
{
    /**
     * @return string
     */
    public function errorMessage()
    {
        return $this->getMessage();
    }
}