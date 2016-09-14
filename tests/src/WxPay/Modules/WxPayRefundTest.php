<?php
namespace Pay\WxPay\Modules;


class WxPayRefundTest extends WxPayDataBaseTest
{
    /**
     * @var WxPayRefund
     */
    protected $model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new WxPayRefund();
    }

    /**
     * @return WxPayRefund
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
        $this->getModel()->setTotalFee(888);
        $this->getModel()->setTransactionId('Vz6WsT7xm6iwJyls');
        $this->getModel()->setOutRefundNo('1415659991120');
        $this->getModel()->setRefundFee(12);
        $this->getModel()->setRefundFeeType('CNY');
        $this->getModel()->setOpUserId('10000100');

        self::assertEquals('wx71be479776815a2a', $this->getModel()->getAppId());
        self::assertEquals('10000100', $this->getModel()->getMchId());
        self::assertEquals('Vz6WsT7xm6iwJyls', $this->getModel()->getNonceStr());
        self::assertEquals('013467007045764', $this->getModel()->getDeviceInfo());
        self::assertEquals('1415659990', $this->getModel()->getOutTradeNo());
        self::assertEquals(888, $this->getModel()->getTotalFee());
        self::assertEquals('Vz6WsT7xm6iwJyls', $this->getModel()->getTransactionId());
        self::assertEquals('1415659991120', $this->getModel()->getOutRefundNo());
        self::assertEquals(12, $this->getModel()->getRefundFee());
        self::assertEquals('CNY', $this->getModel()->getRefundFeeType());
        self::assertEquals('10000100', $this->getModel()->getOpUserId());
    }
}
