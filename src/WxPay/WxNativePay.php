<?php
namespace Pay\WxPay;

use Pay\WxPay\Modules\WxPayNativeAppData;
use Pay\WxPay\Modules\WxPayNativePayData;

class WxNativePay
{
    const NATIVE_URI = "weixin://wap/pay?";

    /**
     * @var array
     */
    private $_config = array();

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->_config  = $config;
    }

    /**
     * 获取配置
     * @return array
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * 参数数组转换为url参数
     * @param array $data
     * @return string
     */
    private function toUrlParams(array $data)
    {
        $buff = "";
        foreach ($data as $k => $v)
        {
            $buff .= $k . "=" . $v . "&";
        }

        return substr($buff, 0, -1);
    }

    /**
     * 生成native支付url
     * @param array $data
     * @return string
     */
    public function createNativeUrl(array $data)
    {
        /**
         * weixin://wap/pay?appid=wx55db64d265e84478&noncestr=cWvJk5MIvxU9&package=WAP&prepayid=wx20150813002643fcb4dd962f0189689566&timestamp=1439396803&sign=5988C3DC7E57A21AE4476A4806A4B7C3
         *
         * appid=wx55db64d265e84478
         * noncestr=cWvJk5MIvxU9
         * package=WAP
         * prepayid=wx20150813002643fcb4dd962f0189689566
         * timestamp=1439396803
         * sign=5988C3DC7E57A21AE4476A4806A4B7C3

         * [appid] => wx71be479776815a2a
        [code_url] => weixin://wxpay/bizpayurl?pr=RKZiyDp
        [mch_id] => 1228744502
        [nonce_str] => Vz6WsT7xm6iwJyls
        [prepay_id] => wx201508122132221b33dfd6990431165182
        [result_code] => SUCCESS
        [return_code] => SUCCESS
        [return_msg] => OK
        [sign] => 2B1A9EBCA09D6A0531CCC40B26362597
        [trade_type] => NATIVE
         */
        $result = new WxPayNativePayData();
        $result->setAppId($this->_config['appId']);
        $result->setNonceStr(substr(WxPayApi::getNonceStr(), 0, 12));
        $result->setPrePayId($data['prepay_id']);
        $result->setTimeStamp(time());
        $result->setSign($result->createSign($this->_config['key']));
        return self::NATIVE_URI . $this->toUrlParams($result->getValues());
    }

    /**
     * 创建app支付所需要的json数据
     * @param array $data
     * @return array
     */
    public function createAppPayParams(array $data)
    {
        $result = new WxPayNativeAppData();
        $result->setAppId($this->_config['appId']);
        $result->setPartnerId($this->_config['mchId']);
        $result->setNonceStr(WxPayApi::getNonceStr());
        $result->setPrePayId($data['prepay_id']);
        $result->setTimeStamp(time());
        $result->setSign($result->createSign($this->_config['key']));

        return $result->getValues();
    }
}
