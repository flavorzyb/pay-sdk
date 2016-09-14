<?php
namespace Pay\WxPay;

use ConfigFactory;

class WxNativePayTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateNativeUrl()
    {
        $config = ConfigFactory::createWxConfig();
        $pay = new WxNativePay($config);
        self::assertEquals($config, $pay->getConfig());
        $result = $pay->createNativeUrl(['prepay_id'=>'wx201508122132221b33dfd6990431165182']);
        self::assertTrue(strlen($result) > 100);
    }

    public function testCreateAppPayParams()
    {
        $config = ConfigFactory::createWxConfig();
        $pay = new WxNativePay($config);
        $result = $pay->createAppPayParams(['prepay_id'=>'wx201508122132221b33dfd6990431165182']);
        self::assertNotEmpty($result);
    }
}
