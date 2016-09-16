<?php
namespace Pay\AliPay\Modules;

class AliPayTradeFundBillTest extends \PHPUnit_Framework_TestCase
{
    public function testFundBill()
    {
        $result = new AliPayTradeFundBill();
        $result->setFundChannel('ALIPAYACCOUNT');
        $result->setAmount(10);
        $result->setRealAmount(10.11);

        self::assertEquals('ALIPAYACCOUNT', $result->getFundChannel());
        self::assertEquals(10, $result->getAmount());
        self::assertEquals(10.11, $result->getRealAmount());
    }
}
