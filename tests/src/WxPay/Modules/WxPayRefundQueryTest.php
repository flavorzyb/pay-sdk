<?php
namespace Pay\WxPay\Modules;


class WxPayRefundQueryTest extends WxPayDataBaseTest
{
    /**
     * @var WxPayRefundQuery
     */
    protected $model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new WxPayRefundQuery();
    }

    /**
     * @return WxPayRefundQuery
     */
    protected function getModel()
    {
        return $this->model;
    }

    public function testOptionsIsMutable()
    {
        parent::testOptionsIsMutable();
        $this->getModel()->setAppId('wx71be479776815a2a');
        $this->getModel()->setMchId('10000100');
        $this->getModel()->setNonceStr('Vz6WsT7xm6iwJyls');
        $this->getModel()->setDeviceInfo('013467007045764');
        $this->getModel()->setOutTradeNo('1415659990');
        $this->getModel()->setTransactionId('Vz6WsT7xm6iwJyls');
        $this->getModel()->setOutRefundNo('1415659991120');
        $this->getModel()->setRefundId('2008450740201411110000174436');

        self::assertEquals('wx71be479776815a2a', $this->getModel()->getAppId());
        self::assertEquals('10000100', $this->getModel()->getMchId());
        self::assertEquals('Vz6WsT7xm6iwJyls', $this->getModel()->getNonceStr());
        self::assertEquals('013467007045764', $this->getModel()->getDeviceInfo());
        self::assertEquals('1415659990', $this->getModel()->getOutTradeNo());
        self::assertEquals('Vz6WsT7xm6iwJyls', $this->getModel()->getTransactionId());
        self::assertEquals('1415659991120', $this->getModel()->getOutRefundNo());
        self::assertEquals('2008450740201411110000174436', $this->getModel()->getRefundId());
    }
}
