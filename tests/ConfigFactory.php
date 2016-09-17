<?php
use Pay\WxPay\Modules\WxPayConfig;
use Pay\AliPay\AliConfig;
class ConfigFactory
{
    private static $CONFIG_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'configs';

    /**
     * @return WxPayConfig
     */
    public static function createWxConfig()
    {
        $result = new WxPayConfig();
        $result->setAppId('wx426b3015555a46be');
        $result->setMchId('1900009851');
        $result->setKey('8934e7d15453e97507ef794cf7b0519d');
        $result->setAppSecret('7813490da6f1265e4901ffb80afaa36f');

        $result->setSslCertPath(self::$CONFIG_PATH . DIRECTORY_SEPARATOR .'wx' . DIRECTORY_SEPARATOR . 'apiclient_cert.pem');
        $result->setSslKeyPath(self::$CONFIG_PATH . DIRECTORY_SEPARATOR .'wx' . DIRECTORY_SEPARATOR . 'apiclient_key.pem');
        $result->setRootCaPath(self::$CONFIG_PATH . DIRECTORY_SEPARATOR .'wx' . DIRECTORY_SEPARATOR .'rootca.pem');

        $result->setCurlProxyHost('0.0.0.0');
        $result->setCurlProxyPort(0);

        $result->setReportLevel(1);
        $result->setNotifyUrl('/Mall/PayResponse/wxPay');
        $result->setCallBackUrl('/Mall/PayResponse/index');
        $result->setMerchantUrl('/Mall/PayResponse/interrupt');

        return $result;
    }

    /**
     * @return AliConfig
     */
    public static function createAliPayConfig()
    {
        $result = new AliConfig();
        $result->setAppId('2016091600523436');
        $result->setPrivateKeyPath(self::$CONFIG_PATH . DIRECTORY_SEPARATOR .'alipay' . DIRECTORY_SEPARATOR .'app_private_key.pem');
        $result->setPublicKeyPath(self::$CONFIG_PATH . DIRECTORY_SEPARATOR .'alipay' . DIRECTORY_SEPARATOR . 'alipay_public_key.pem');

        $result->setNotifyUrl('http://upgrade.zhuyanbin.cn/notify.php');
        $result->setCallBackUrl('http://upgrade.zhuyanbin.cn/notify.php');
        $result->setMerchantUrl('http://upgrade.zhuyanbin.cn/notify.php');
        $result->setSellerId('2088102175865018');
        $result->setGateWayUrl('https://openapi.alipaydev.com/gateway.do');

        return $result;
    }
}
