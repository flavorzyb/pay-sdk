<?php
namespace Pay\WxPay;


use Pay\WxPay\Modules\WxPayConfig;
use Pay\WxPay\Modules\WxPayJsApiPay;

class WxJsApiPay
{
    /**
     * @var WxPayConfig
     */
    private $config = null;

    /**
     * @param WxPayConfig $config
     */
    public function __construct(WxPayConfig $config)
    {
        $this->config  = $config;
    }

    /**
     * 获取配置
     * @return WxPayConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     *
     * 获取jsapi支付的参数
     *
     * @param   array   $unifiedOrderResult 统一支付接口返回的数据
     * @return  string                      json数据，可直接填入js函数作为参数
     */
    public function createJsApiParameters(array $unifiedOrderResult)
    {
        if(!array_key_exists("appid", $unifiedOrderResult)
            || !array_key_exists("prepay_id", $unifiedOrderResult)
            || $unifiedOrderResult['prepay_id'] == "")
        {
            return false;
        }

        $jsPayData  = new WxPayJsApiPay();
        $jsPayData->setAppId($this->config->getAppId());
        $jsPayData->setTimeStamp(time());
        $jsPayData->setNonceStr(WxPayApi::getNonceStr());
        $jsPayData->setPackage("prepay_id=" . $unifiedOrderResult['prepay_id']);
        $jsPayData->setSignType("MD5");
        $jsPayData->setPaySign($jsPayData->createSign($this->config->getKey()));

        return json_encode($jsPayData->getValues());
    }
}
