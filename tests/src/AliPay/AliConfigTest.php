<?php
namespace Pay\AliPay;

class AliConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testOptionsIsMutable()
    {
        $config = new AliConfig();
        $config->setAppId('20884213451476760');
        $config->setPrivateKeyPath('rsa_private_key.pem');
        $config->setPublicKeyPath('alipay_public_key.pem');

        $config->setNotifyUrl('/Mall/PayResponse/wxPay');
        $config->setCallBackUrl('/Mall/PayResponse/index');
        $config->setMerchantUrl('/Mall/PayResponse/interrupt');
        $config->setSellerId('2088102175865018');
        $config->setGateWayUrl('https://openapi.alipaydev.com/gateway.do');

        self::assertEquals('20884213451476760', $config->getAppId());
        self::assertEquals('rsa_private_key.pem', $config->getPrivateKeyPath());
        self::assertEquals('alipay_public_key.pem', $config->getPublicKeyPath());

        self::assertEquals('/Mall/PayResponse/wxPay', $config->getNotifyUrl());
        self::assertEquals('/Mall/PayResponse/index', $config->getCallBackUrl());
        self::assertEquals('/Mall/PayResponse/interrupt', $config->getMerchantUrl());
        self::assertEquals('2088102175865018', $config->getSellerId());
        self::assertEquals('https://openapi.alipaydev.com/gateway.do', $config->getGateWayUrl());
    }
}
