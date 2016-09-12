<?php
namespace Pay\WxPay\Modules;


class WxPayNativeAppDataTest extends WxPayDataBaseTest
{
    /**
     * @var WxPayNativeAppData
     */
    protected $model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new WxPayNativeAppData();
    }

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
        $this->getModel()->setPrePayId('wx201508122132221b33dfd6990431165182');
        $this->getModel()->setPartnerId('wx71be479776815a2a');

        self::assertEquals('wx71be479776815a2a', $this->getModel()->getAppId());
        self::assertEquals($time, $this->getModel()->getTimeStamp());
        self::assertEquals('Vz6WsT7xm6iwJyls', $this->getModel()->getNonceStr());
        self::assertEquals('wx201508122132221b33dfd6990431165182', $this->getModel()->getPrePayId());
        self::assertEquals('wx71be479776815a2a', $this->getModel()->getPartnerId());

        self::assertTrue($this->getModel()->isSetAppId());
        self::assertTrue($this->getModel()->isSetNonceStr());
        self::assertTrue($this->getModel()->isSetTimeStamp());
        self::assertTrue($this->getModel()->isSetPrePayId());
    }
}
