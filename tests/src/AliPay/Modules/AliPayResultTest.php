<?php
namespace Pay\AliPay\Modules;

abstract class AliPayResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return AliPayResult
     */
    abstract protected function getModel();

    public function testOptionIsMutable()
    {
        $this->getModel()->setCode(40004);
        $this->getModel()->setSubCode('ACQ.TRADE_HAS_SUCCESS');
        $this->getModel()->setMsg('Business Failed');
        $this->getModel()->setSubMsg('交易已被支付');
        $this->getModel()->setSign('DZXh8eeTuAHoYE3w1J+POiPhfDxOYBfUNn1lkeT/V7P4zJdyojWEa6IZs6Hz0yDW5Cp/viufUb5I0/V5WENS3OYR8zRedqo6D+fUTdLHdc+EFyCkiQhBxIzgngPdPdfp1PIS7BdhhzrsZHbRqb7o4k3Dxc+AAnFauu4V6Zdwczo=');

        self::assertEquals('40004', $this->getModel()->getCode());
        self::assertEquals('ACQ.TRADE_HAS_SUCCESS', $this->getModel()->getSubCode());
        self::assertEquals('Business Failed', $this->getModel()->getMsg());
        self::assertEquals('交易已被支付', $this->getModel()->getSubMsg());
        self::assertEquals('DZXh8eeTuAHoYE3w1J+POiPhfDxOYBfUNn1lkeT/V7P4zJdyojWEa6IZs6Hz0yDW5Cp/viufUb5I0/V5WENS3OYR8zRedqo6D+fUTdLHdc+EFyCkiQhBxIzgngPdPdfp1PIS7BdhhzrsZHbRqb7o4k3Dxc+AAnFauu4V6Zdwczo=', $this->getModel()->getSign());
    }
}
