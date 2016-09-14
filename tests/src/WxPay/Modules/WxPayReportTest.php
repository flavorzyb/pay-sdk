<?php
namespace Pay\WxPay\Modules;

class WxPayReportTest extends WxPayDataBaseTest
{
    /**
     * @var WxPayReport
     */
    protected $model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->model = new WxPayReport();
    }

    /**
     * @return WxPayReport
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
        $this->getModel()->setErrCode(-10);
        $this->getModel()->setInterfaceUrl('https://api.mch.weixin.qq.com/pay/unifiedorder');
        $this->getModel()->setExecuteTime(1000);
        $this->getModel()->setTime('20091227091010');
        $this->getModel()->setErrCodeDes('签名失败');
        $this->getModel()->setReturnCode('FAIL');
        $this->getModel()->setResultCode('FAIL');
        $this->getModel()->setReturnMsg('签名失败');
        $this->getModel()->setOutTradeNo('1415659990');
        $this->getModel()->setUserIp('127.0.0.1');

        self::assertEquals('wx71be479776815a2a', $this->getModel()->getAppId());
        self::assertEquals('10000100', $this->getModel()->getMchId());
        self::assertEquals('Vz6WsT7xm6iwJyls', $this->getModel()->getNonceStr());
        self::assertEquals('013467007045764', $this->getModel()->getDeviceInfo());
        self::assertEquals(-10, $this->getModel()->getErrCode());
        self::assertEquals('https://api.mch.weixin.qq.com/pay/unifiedorder', $this->getModel()->getInterfaceUrl());
        self::assertEquals('1000', $this->getModel()->getExecuteTime());
        self::assertEquals('20091227091010', $this->getModel()->getTime());
        self::assertEquals('签名失败', $this->getModel()->getErrCodeDes());
        self::assertEquals('FAIL', $this->getModel()->getResultCode());
        self::assertEquals('FAIL', $this->getModel()->getReturnCode());
        self::assertEquals('签名失败', $this->getModel()->getReturnMsg());
        self::assertEquals('1415659990', $this->getModel()->getOutTradeNo());
        self::assertEquals('127.0.0.1', $this->getModel()->getUserIp());
    }
}
