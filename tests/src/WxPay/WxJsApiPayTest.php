<?php
namespace Pay\WxPay;


class WxJsApiPayTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateJsApiParameters()
    {
        $config = include __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
        $pay = new WxJsApiPay($config);
        self::assertEquals($config, $pay->getConfig());
        $result = $pay->createJsApiParameters(['prepay_id' => 'wx201508122132221b33dfd6990431165182', 'appid' => $config['appId']]);
        self::assertTrue(strlen($result) > 10);

        $result = $pay->createJsApiParameters(['prepay_id' => 'wx201508122132221b33dfd6990431165182',]);
        self::assertFalse($result);
    }
}
