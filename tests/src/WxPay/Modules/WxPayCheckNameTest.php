<?php

namespace Pay\WxPay\Modules;


class WxPayCheckNameTest extends \PHPUnit_Framework_TestCase
{
    public function testCheckName()
    {
        $result = new WxPayCheckName(WxPayCheckName::FORCE_CHECK);
        self::assertEquals(WxPayCheckName::FORCE_CHECK, $result->getValue());
        self::assertTrue($result->isForceCheck());
        self::assertFalse($result->isNoCheck());
        self::assertFalse($result->isOptionCheck());

        $result = new WxPayCheckName(WxPayCheckName::OPTION_CHECK);
        self::assertEquals(WxPayCheckName::OPTION_CHECK, $result->getValue());
        self::assertFalse($result->isForceCheck());
        self::assertFalse($result->isNoCheck());
        self::assertTrue($result->isOptionCheck());


        $result = new WxPayCheckName(WxPayCheckName::NO_CHECK);
        self::assertEquals(WxPayCheckName::NO_CHECK, $result->getValue());
        self::assertFalse($result->isForceCheck());
        self::assertTrue($result->isNoCheck());
        self::assertFalse($result->isOptionCheck());


        $result = new WxPayCheckName('');
        self::assertEquals(WxPayCheckName::OPTION_CHECK, $result->getValue());
        self::assertFalse($result->isForceCheck());
        self::assertFalse($result->isNoCheck());
        self::assertTrue($result->isOptionCheck());
    }
}
