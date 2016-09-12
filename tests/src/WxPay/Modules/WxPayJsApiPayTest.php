<?php
namespace Pay\WxPay\Modules;

class WxPayJsApiPayTest extends WxPayDataBaseTest
{
    /**
     * @var WxPayJsApiPay
     */
    protected $model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new WxPayJsApiPay();
    }

    /**
     * @return WxPayJsApiPay
     */
    protected function getModel()
    {
        return $this->model;
    }

    public function testOptionsIsMutable()
    {
        parent::testOptionsIsMutable();
        $time = time();
        $this->getModel()->setAppId('wx71be479776815a2a');
        $this->getModel()->setTimeStamp($time);
        $this->getModel()->setNonceStr('Vz6WsT7xm6iwJyls');
        $this->getModel()->setPackage('JSAPI');
        $this->getModel()->setSignType('md5');
        $this->getModel()->setPaySign('2B1A9EBCA09D6A0531CCC40B26362597');

        self::assertEquals('wx71be479776815a2a', $this->getModel()->getAppId());
        self::assertEquals($time, $this->getModel()->getTimeStamp());
        self::assertEquals('Vz6WsT7xm6iwJyls', $this->getModel()->getNonceStr());
        self::assertEquals('JSAPI', $this->getModel()->getPackage());
        self::assertEquals('md5', $this->getModel()->getSignType());
        self::assertEquals('2B1A9EBCA09D6A0531CCC40B26362597', $this->getModel()->getPaySign());

        self::assertTrue($this->getModel()->isSetAppId());
        self::assertTrue($this->getModel()->isSetNonceStr());
        self::assertTrue($this->getModel()->isSetTimeStamp());
        self::assertTrue($this->getModel()->isSetPackage());
        self::assertTrue($this->getModel()->isSetSignType());
        self::assertTrue($this->getModel()->isSetPaySign());
    }

    /**
     * @expectedException Pay\WxPay\WxPayException
     */
    public function testEmptyToXmlThrowsException()
    {
        $this->getModel()->toXml();
    }
}
