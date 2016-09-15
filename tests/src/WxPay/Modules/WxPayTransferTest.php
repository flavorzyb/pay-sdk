<?php
namespace Pay\WxPay\Modules;


class WxPayTransferTest extends WxPayDataBaseTest
{
    /**
     * @var WxPayTransfer
     */
    protected $model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new WxPayTransfer();
    }

    /**
     * @return WxPayTransfer
     */
    protected function getModel()
    {
        return $this->model;
    }

    public function testOptionsIsMutable()
    {
        parent::testOptionsIsMutable();
        $this->getModel()->setAppId('wx71be479776815a2a');
        $this->getModel()->setNonceStr('Vz6WsT7xm6iwJyls');
        $this->getModel()->setDeviceInfo('013467007045764');
        $this->getModel()->setOpenId('oUpF8uMuAJO_M2pxb1Q9zNjWeS6o');
        $this->getModel()->setSpbillCreateIp('127.0.0.1');
        $this->getModel()->setMchId('10000100');
        $this->getModel()->setPartnerTradeNo('10000098201411111234567890');
        $this->getModel()->setCheckName(new WxPayCheckName(WxPayCheckName::FORCE_CHECK));
        $this->getModel()->setReUserName('马花花');
        $this->getModel()->setAmount(100);
        $this->getModel()->setDescription('理赔');

        self::assertEquals('wx71be479776815a2a', $this->getModel()->getAppId());
        self::assertEquals('Vz6WsT7xm6iwJyls', $this->getModel()->getNonceStr());
        self::assertEquals('013467007045764', $this->getModel()->getDeviceInfo());
        self::assertEquals('oUpF8uMuAJO_M2pxb1Q9zNjWeS6o', $this->getModel()->getOpenId());
        self::assertEquals('127.0.0.1', $this->getModel()->getSpbillCreateIp());
        self::assertEquals('10000100', $this->getModel()->getMchId());
        self::assertEquals('10000098201411111234567890', $this->getModel()->getPartnerTradeNo());
        self::assertEquals(WxPayCheckName::FORCE_CHECK, $this->getModel()->getCheckName());
        self::assertEquals('马花花', $this->getModel()->getReUserName());
        self::assertEquals(100, $this->getModel()->getAmount());
        self::assertEquals('理赔', $this->getModel()->getDescription());
    }
}
