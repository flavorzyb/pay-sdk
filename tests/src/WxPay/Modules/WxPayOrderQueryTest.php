<?php
namespace Pay\WxPay\Modules;


class WxPayOrderQueryTest extends WxPayDataBaseTest
{
    /**
     * @var WxPayOrderQuery
     */
    protected $model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new WxPayOrderQuery();
    }

    /**
     * @return WxPayOrderQuery
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
        $this->getModel()->setTransactionId('1008450740201411110005820873');
        $this->getModel()->setOutTradeNo('20150806125346');

        self::assertEquals('wx71be479776815a2a', $this->getModel()->getAppId());
        self::assertEquals('10000100', $this->getModel()->getMchId());
        self::assertEquals('Vz6WsT7xm6iwJyls', $this->getModel()->getNonceStr());
        self::assertEquals('1008450740201411110005820873', $this->getModel()->getTransactionId());
        self::assertEquals('20150806125346', $this->getModel()->getOutTradeNo());
    }
}
