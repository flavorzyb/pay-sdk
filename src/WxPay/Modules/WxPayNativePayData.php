<?php
namespace Pay\WxPay\Modules;


class WxPayNativePayData extends WxPayDataBase
{
    public function __construct()
    {
        $this->values['package'] = 'NATIVE';
    }

    /**
     * 设置微信分配的公众账号ID
     * @param string $value
     **/
    public function setAppId($value)
    {
        $this->values['appid'] = trim($value);
    }

    /**
     * 获取微信分配的公众账号ID的值
     * @return string
     **/
    public function getAppId()
    {
        return $this->get('appid');
    }

    /**
     * 设置支付时间戳
     * @param string $value
     **/
    public function setTimeStamp($value)
    {
        $this->values['timestamp'] = trim($value);
    }

    /**
     * 获取支付时间戳的值
     * @return string
     **/
    public function getTimeStamp()
    {
        return $this->get('timestamp');
    }

    /**
     * 设置随机字符串
     * @param string $value
     **/
    public function setNonceStr($value)
    {
        $this->values['noncestr'] = trim($value);
    }

    /**
     * 获取随机字符串的值
     * @return string
     **/
    public function getNonceStr()
    {
        return $this->get('noncestr');
    }

    /**
     * @param string $value
     **/
    public function setPrePayId($value)
    {
        $this->values['prepayid'] = trim($value);
    }

    /**
     * @return string
     **/
    public function getPrePayId()
    {
        return $this->get('prepayid');
    }

    public function setPartnerId($partnerId)
    {
        $this->values['partnerid'] = trim($partnerId);
    }

    public function getPartnerId()
    {
        return $this->get('partnerid');
    }
}
