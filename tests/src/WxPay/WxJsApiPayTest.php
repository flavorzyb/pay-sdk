<?php
namespace Pay\WxPay;

use ConfigFactory;

class WxJsApiPayTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateJsApiParameters()
    {
        $config = ConfigFactory::createWxConfig();
        $pay = new WxJsApiPay($config);
        self::assertEquals($config, $pay->getConfig());
        $result = $pay->createJsApiParameters(['prepay_id' => 'wx201508122132221b33dfd6990431165182', 'appid' => $config->getAppId()]);
        self::assertTrue(strlen($result) > 10);

        $result = $pay->createJsApiParameters(['prepay_id' => 'wx201508122132221b33dfd6990431165182',]);
        self::assertFalse($result);
    }
}
