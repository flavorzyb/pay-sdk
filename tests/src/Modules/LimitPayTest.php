<?php
namespace Pay\Modules;

class LimitPayTest extends \PHPUnit_Framework_TestCase
{
    public function testLimitPay()
    {
        $limitPay = new LimitPay(LimitPay::NORMAL);
        self::assertEquals(LimitPay::NORMAL, $limitPay->getValue());
        self::assertFalse($limitPay->isNoCredit());

        $limitPay = new LimitPay(LimitPay::NO_CREDIT);
        self::assertEquals(LimitPay::NO_CREDIT, $limitPay->getValue());
        self::assertTrue($limitPay->isNoCredit());
    }
}
