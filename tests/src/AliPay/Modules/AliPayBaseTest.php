<?php
namespace Pay\AliPay\Modules;

abstract class AliPayBaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return AliPayBase
     */
    abstract protected function getModel();

    public function testOptionsIsMutable()
    {
        self::assertEquals(AliPayBase::FORMAT, $this->getModel()->getFormat());
        self::assertEquals(AliPayBase::RSA, $this->getModel()->getSignType());
        self::assertEquals(AliPayBase::VERSION, $this->getModel()->getVersion());

        $time = date('Y-m-d H:i:s');
        $this->getModel()->setAppId('2014072300007148');
        $this->getModel()->setSign('ERITJKEIJKJHKKKKKKKHJEREEEEEEEEEEE');
        $this->getModel()->setTimeStamp($time);
        $this->getModel()->setCharset(AliPayCharset::createGBKCharset());

        self::assertEquals('2014072300007148', $this->getModel()->getAppId());
        self::assertEquals('ERITJKEIJKJHKKKKKKKHJEREEEEEEEEEEE', $this->getModel()->getSign());
        self::assertEquals($time, $this->getModel()->getTimeStamp());
        self::assertEquals(AliPayCharset::createGBKCharset(), $this->getModel()->getCharset());
    }
}
