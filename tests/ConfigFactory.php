<?php
use Pay\WxPay\Modules\WxPayConfig;
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
}
