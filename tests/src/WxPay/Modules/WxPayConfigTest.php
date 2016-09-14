<?php
namespace Pay\WxPay\Modules;


class WxPayConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testOptionsIsMutable()
    {
        $config = new WxPayConfig();
        $config->setAppId('wx426b3015555a46be');
        $config->setMchId('1900009851');
        $config->setKey('8934e7d15453e97507ef794cf7b0519d');
        $config->setAppSecret('7813490da6f1265e4901ffb80afaa36f');

        $config->setSslCertPath('cert/apiclient_cert.pem');
        $config->setSslKeyPath('cert/apiclient_key.pem');
        $config->setRootCaPath('cert/rootca.pem');

        $config->setCurlProxyHost('10.152.18.220');
        $config->setCurlProxyPort('8080');

        $config->setReportLevel(1);
        $config->setNotifyUrl('/Mall/PayResponse/wxPay');
        $config->setCallBackUrl('/Mall/PayResponse/index');
        $config->setMerchantUrl('/Mall/PayResponse/interrupt');

        self::assertEquals('wx426b3015555a46be', $config->getAppId());
        self::assertEquals('1900009851', $config->getMchId());
        self::assertEquals('8934e7d15453e97507ef794cf7b0519d', $config->getKey());
        self::assertEquals('7813490da6f1265e4901ffb80afaa36f', $config->getAppSecret());

        self::assertEquals('cert/apiclient_cert.pem', $config->getSslCertPath());
        self::assertEquals('cert/apiclient_key.pem', $config->getSslKeyPath());
        self::assertEquals('cert/rootca.pem', $config->getRootCaPath());

        self::assertEquals('10.152.18.220', $config->getCurlProxyHost());
        self::assertEquals(8080, $config->getCurlProxyPort());

        self::assertEquals(1, $config->getReportLevel());
        self::assertEquals('/Mall/PayResponse/wxPay', $config->getNotifyUrl());
        self::assertEquals('/Mall/PayResponse/index', $config->getCallBackUrl());
        self::assertEquals('/Mall/PayResponse/interrupt', $config->getMerchantUrl());
    }
}
