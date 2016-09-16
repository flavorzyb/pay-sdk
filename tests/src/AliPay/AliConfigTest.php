<?php
namespace Pay\AliPay;

class AliConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testOptionsIsMutable()
    {
        $config = new AliConfig();
        $config->setAppId('20884213451476760');
        $config->setKey('9bb03rsrl1la3icy2eph8hpqwy7jzz0i');
        $config->setPrivateKeyPath('rsa_private_key.pem');
        $config->setPublicKeyPath('alipay_public_key.pem');

        $config->setSignType('0001');
        $config->setInputCharset('utf-8');
        $config->setCertPath('cacert.pem');
        $config->setTransport('http');

        $config->setAccount('test@163.com');
        $config->setNotifyUrl('/Mall/PayResponse/wxPay');
        $config->setCallBackUrl('/Mall/PayResponse/index');
        $config->setMerchantUrl('/Mall/PayResponse/interrupt');

        self::assertEquals('20884213451476760', $config->getPartnerId());
        self::assertEquals('9bb03rsrl1la3icy2eph8hpqwy7jzz0i', $config->getKey());
        self::assertEquals('rsa_private_key.pem', $config->getPrivateKeyPath());
        self::assertEquals('alipay_public_key.pem', $config->getPublicKeyPath());

        self::assertEquals('0001', $config->getSignType());
        self::assertEquals('utf-8', $config->getInputCharset());
        self::assertEquals('cacert.pem', $config->getCertPath());
        self::assertEquals('http', $config->getTransport());

        self::assertEquals('test@163.com', $config->getAccount());
        self::assertEquals('/Mall/PayResponse/wxPay', $config->getNotifyUrl());
        self::assertEquals('/Mall/PayResponse/index', $config->getCallBackUrl());
        self::assertEquals('/Mall/PayResponse/interrupt', $config->getMerchantUrl());
    }
}
